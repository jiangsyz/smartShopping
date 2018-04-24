#!/bin/bash
#待发货切换待收货
while true; 
do
	curl "http://app1.zhengshan.store/smartTask/backend/web/index.php?r=task/api-delivered"
	sleep 0.1
done