#!/bin/bash
#订单返库存检查
while true; 
do
	curl "http://app1.zhengshan.store/smartShopping/backend/web/index.php?r=task/api-back-keep-count"
	sleep 0.1
done