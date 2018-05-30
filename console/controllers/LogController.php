<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use backend\models\actionTracker\actionTracker;
class LogController extends Controller{
	//用户行为追踪
    public function actionActionTracker(){
    	while(1){
    		try{
				//开启事务
				$trascation=Yii::$app->db->beginTransaction();
				//找到跟踪过的最后一条日志
				$lastOne=actionTracker::find()->orderBy("`logId` DESC")->one();
				//从下一条起继续跟踪
				$startId=$lastOne?$lastOne->logId+1:1;
				actionTracker::actionTrack($startId);
				//提交事务
				$trascation->commit();
				//休息一下
				sleep(5);
    		}
	    	catch(Exception $e){
	    		//回滚
				$trascation->rollback();
	    	}
    	}
    }
}
?>