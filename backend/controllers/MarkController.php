<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\token\tokenManagement;
use backend\models\mark\mark;
class MarkController extends SmartWebController{
	//标记
	public function actionApiMark(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$member=tokenManagement::getManagement(Yii::$app->request->get('token',false),array(source::TYPE_MEMBER))->getOwner();
			//获取标记类型
			$markType=Yii::$app->request->get('markType',false);
			if(!$markType) throw new SmartException("miss markType");
			//获取资源类型
			$sourceType=Yii::$app->request->get('sourceType',false);
			if(!$sourceType) throw new SmartException("miss sourceType");
			//获取资源id
			$sourceId=Yii::$app->request->get('sourceId',false);
			if(!$sourceId) throw new SmartException("miss sourceId");
			//增加标记
			$markInfo=array();
			$markInfo['memberId']=$member->id;
			$markInfo['markType']=$markType;
			$markInfo['sourceType']=$sourceType;
			$markInfo['sourceId']=$sourceId;
			$mark=mark::addObj($markInfo);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$mark->id));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
}