<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\console\SmartDaemonController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\member\publicAccountUser;
class MemberController extends SmartDaemonController{
	//获取公众号的全部用户(openid)
    public function actionDaemonGetOpenidFromPublicAccount(){
    	$this->begin();
    	//循环处理
    	while(1){
    		try{
				//开启事务
				$trascation=Yii::$app->db->beginTransaction();
				//获取全量用户
				$appId=Yii::$app->params["app2"]["appId"];
				$appSecret=Yii::$app->params["app2"]["appSecret"];
				$openids=Yii::$app->smartWechat->getOpenidsFromPublicAccount($appId,$appSecret);
				//新增用户量
				$increment=0;
				//入库
				foreach($openids as $v){
					//已经存在的忽略
					$user=publicAccountUser::find()->where("`appid`='{$appId}' AND `openid`='{$v}'")->one();
					if($user) continue;
					//新增用户
					$userData=array();
					$userData['appid']=$appId;
					$userData['openid']=$v;
					$user=publicAccountUser::addObj($userData);
					$increment++;
					var_dump($increment);
				}
				//记录日志
				Yii::$app->smartLog->consoleLog('increment='.$increment);
				//提交事务
				$trascation->commit();
    		}
	    	catch(Exception $e){
	    		//回滚
				$trascation->rollback();
	    	}
	    	//休息一下
			$this->sleep(60*10);
			//报告存活
			$this->alive();
    	}
    }
    //========================================
    //获取公众号用户的unionid
    public function actionDaemonGetUnionidFromPublicAccount(){
    	
    }
}
?>