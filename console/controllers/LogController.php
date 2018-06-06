<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\actionTracker\actionTracker;
class LogController extends Controller{
	//用户行为追踪
    public function actionActionTracker(){
    	//记录日志
    	Yii::$app->smartLog->consoleLog('begin');
    	//循环处理
    	while(1){
    		try{
				//开启事务
				$trascation=Yii::$app->db->beginTransaction();
				//找到跟踪过的最后一条日志
				$lastOne=actionTracker::find()->orderBy("`logId` DESC")->one();
				//确定跟踪日志的起始id
				$startId=$lastOne?$lastOne->logId+1:1;
				//记录日志
				Yii::$app->smartLog->consoleLog('log startId='.$startId);
				//从起始id开始进行行为跟踪
				actionTracker::actionTrack($startId);
				//提交事务
				$trascation->commit();
    		}
	    	catch(Exception $e){
	    		//回滚
				$trascation->rollback();
	    	}
	    	//休息一下
			sleep(Yii::$app->params["consoleSleep"]);
    	}
    }
}
?>