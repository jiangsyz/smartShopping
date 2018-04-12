#!/bin/bash
#订单支付超时检测
while true; 
do
	curl "http://app1.zhengshan.store/smartShopping/backend/web/index.php?r=task/api-check-pay-time-out-order"
	sleep 0.1
done