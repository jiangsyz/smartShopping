<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\console\SmartDaemonController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\member\publicAccountUser;
use backend\models\member\member;
class MemberController extends SmartDaemonController{
	//获取公众号用户的openid
    public function actionDaemonGetOpenidFromPublicAccount(){
    	$this->begin();
    	//循环处理
    	while(1){
    		try{
				//开启事务
				$trascation=Yii::$app->db->beginTransaction();
				//获取公众号相关配置
				$appId=Yii::$app->params["app2"]["appId"];
				$appSecret=Yii::$app->params["app2"]["appSecret"];
				//获取全量用户
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
    	$this->begin();
    	//获取公众号相关配置
		$appId=Yii::$app->params["app2"]["appId"];
		$appSecret=Yii::$app->params["app2"]["appSecret"];
    	//循环处理
    	while(1){
    		//获取缺失unionid信息的用户
    		$rows=publicAccountUser::find()->where("`appid`='{$appId}' AND `unionid` IS NULL")->orderBy("`id` ASC")->offset(0)->limit(100)->all();
			foreach($rows as $row){
				try{
					//开启事务
					$trascation=Yii::$app->db->beginTransaction();
					//获取unionid
					$unionid=Yii::$app->smartWechat->getUnionidFromPublicAccount($appId,$appSecret,$row->openid);
					//记录数据
					$row->updateObj(array('unionid'=>$unionid));
					//提交事务
					$trascation->commit();
				}
				catch(Exception $e){$trascation->rollback();}
			}
			//计算剩余unionid缺失的用户数量
			$remaining=publicAccountUser::find()->where("`appid`='{$appId}' AND `unionid` IS NULL")->count();
			//记录日志
			Yii::$app->smartLog->consoleLog('remaining='.$remaining);
    	}
    }
    //========================================
    //获取某个用户的unionid
    public function actionGetUserUnionid($openId){
    	$this->begin();
    	//获取公众号相关配置
		$appId=Yii::$app->params["app2"]["appId"];
		$appSecret=Yii::$app->params["app2"]["appSecret"];
    	try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取指定openid的用户
			$row=publicAccountUser::find()->where("`appid`='{$appId}' AND `openId`='{$openId}'")->all();
			if(!$row) throw new SmartException("miss row");
			//获取unionid
			$unionid=Yii::$app->smartWechat->getUnionidFromPublicAccount($appId,$appSecret,$row->openid);
			//记录数据
			$row->updateObj(array('unionid'=>$unionid));
			//提交事务
			$trascation->commit();
		}
		catch(Exception $e){
			$trascation->rollback();
			var_dump($e->getMessage());
		}
    }
}
?>