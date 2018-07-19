<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\console\SmartDaemonController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\notice\notice;
use backend\models\member\member;
class NoticeController extends SmartDaemonController{
	//发送通知
    public function actionDaemonSendNotice(){
    	$this->begin();
    	//循环处理
    	while(1){
    		//查5分钟内产生的通知
    		$time=time()-60*5;
    		$table=notice::tableName();
    		$sql="SELECT * FROM {$table} WHERE `sendStatus`='0' AND `createTime`>='{$time}' LIMIT 100 FOR UPDATE";
			$notices=notice::findBySql($sql)->all();    		
			//发送
			foreach($notices as $notice){
				try{
					//开启事务
					$trascation=Yii::$app->db->beginTransaction();
					//记录日志
					Yii::$app->smartLog->consoleLog("send notice {$notice->id}");
					//获取用户
					$member=member::find()->where("`id`='{$notice->memberId}'")->one();
					if(!$member) throw new SmartException("miss member {$notice->memberId}");
					Yii::$app->smartSms->send($member->phone,$notice->content);
					//标记
					$notice->updateObj(array('sendStatus'=>1,'sendTime'=>time()));
					//提交事务
					$trascation->commit();
    			}
    			catch(Exception $e){
	    			//回滚
					$trascation->rollback();
					//记录报错
					$notice->updateObj(array('sendStatus'=>-1,'sendTime'=>time()));
	    		}
			}
	    	//休息一下
			$this->sleep();
			//报告存活
			$this->alive();
    	}
    }
}
?>