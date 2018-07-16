#!/bin/bash
#感谢吴佳玮的辛勤付出
source /etc/profile
date=`date +%F`
d0=('log/daemon-action-tracker' 'nohup /usr/local/nginx/html/smartShopping/./yii log/daemon-action-tracker >> log_daemon_action_tracker.log_'${date}' 2>> log_daemon_action_tracker.err_'${date}' &')
d1=('order/daemon-check-pay-timeout' 'nohup /usr/local/nginx/html/smartShopping/./yii order/daemon-check-pay-timeout >> order_daemon_check_pay_timeout.log_'${date}' 2>> order_daemon_check_pay_timeout.err_'${date}' &')
d2=('order/daemon-back-keep-count' 'nohup /usr/local/nginx/html/smartShopping/./yii order/daemon-back-keep-count >> order_daemon_back_keep_count.log_'${date}' 2>> order_daemon_back_keep_count.err_'${date}' &')
d3=('order/daemon-delivered' 'nohup /usr/local/nginx/html/smartShopping/./yii order/daemon-delivered >> order_daemon_delivered.log_'${date}' 2>> order_daemon_delivered.err_'${date}' &')
d4=('member/daemon-get-openid-from-public-account' 'nohup /usr/local/nginx/html/smartShopping/./yii member/daemon-get-openid-from-public-account >> member_daemon_get_openid_from_public_account.log_'${date}' 2>> member_daemon_get_openid_from_public_account.err_'${date}' &')
d5=('member/daemon-get-unionid-from-public-account' 'nohup /usr/local/nginx/html/smartShopping/./yii member/daemon-get-unionid-from-public-account >> member_daemon_get_unionid_from_public_account.log_'${date}' 2>> member_daemon_get_unionid_from_public_account.err_'${date}' &')
d6=('product/daemon-sku-auto-close' 'nohup /usr/local/nginx/html/smartShopping/./yii product/daemon-sku-auto-close >> product_daemon_sku_auto_close.log_'${date}' 2>> product_daemon_sku_auto_close.err_'${date}' &')
d7=('product/daemon-spu-auto-close' 'nohup /usr/local/nginx/html/smartShopping/./yii product/daemon-spu-auto-close >> product_daemon_spu_auto_close.log_'${date}' 2>> product_daemon_spu_auto_close.err_'${date}' &')
d8=('product/daemon-del-recommend-record' 'nohup /usr/local/nginx/html/smartShopping/./yii product/daemon-del-recommend-record >> product_daemon_del_recommend_record.log_'${date}' 2>> product_daemon_del_recommend_record.err_'${date}' &')
#逐个检测启动
for i in $(seq 0 8)
do
		#跳过product/daemon-sku-auto-close
		if [ "${i}" -eq "6" ];then 
			continue; 
		fi
		#检测,进程不在重新启动
        eval check=\${d$i[0]};
        eval command=\${d$i[1]};
        psCommand=`ps aux | grep ${check} | grep -v grep | wc -l`;
        if [ "${psCommand}" -lt "1" ];then
                eval $command;
        fi
done