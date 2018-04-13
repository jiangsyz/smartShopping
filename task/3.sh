#!/bin/bash
#推送通知
while true; 
do
	curl "http://app1.zhengshan.store/smartTask/backend/web/index.php?r=task/api-push-notice"
	sleep 0.1
done