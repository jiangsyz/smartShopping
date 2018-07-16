<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\console\SmartDaemonController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\actionTracker\actionTracker;
class LogController extends SmartDaemonController{
	//用户行为追踪
    public function actionDaemonActionTracker(){
    	$this->begin();
    	//找到跟踪过的最后一条日志
		$lastOne=actionTracker::find()->orderBy("`logId` DESC")->one();
		//确定跟踪日志的起始id
		$startId=$lastOne?$lastOne->logId+1:1;
    	//循环处理
    	while(1){
    		try{
				//开启事务
				$trascation=Yii::$app->db->beginTransaction();
				//记录日志
				Yii::$app->smartLog->consoleLog('log startId='.$startId);
				//从起始id开始进行行为跟踪
				$startId=actionTracker::actionTrack($startId);
				//提交事务
				$trascation->commit();
    		}
	    	catch(Exception $e){
	    		//回滚
				$trascation->rollback();
				//记录报错
				Yii::$app->smartLog->consoleLog('exception='.$e->getMessage());
	    	}
	    	//休息一下
			$this->sleep();
			//报告存活
			$this->alive();
    	}
    }
}
?>