<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\token\tokenManagement;
use backend\models\model\source;
class AreaController extends SmartWebController{
	//获取子地域
	public function actionApiGetChildAreas(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取父级地域id
			$parentId=$this->requestGet('parentId',0);
			//获取子地域
			$childAreas=Yii::$app->smartArea->getChildAreas($parentId);
			//组织数据
			foreach($childAreas as $child) $data[]=$child->getData();
			//提交事务
			$trascation->commit();
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(Exception $e){
    		//回滚
			$trascation->rollback();
    		$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//获取某个地域
	public function actionApiGetArea(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取父级地域id
			$areaId=$this->requestGet('areaId',0);
			//获取地域
			$area=Yii::$app->smartArea->getArea($areaId); if(!$area) throw new SmartException("miss area");
			//提交事务
			$trascation->commit();
			$this->response(1,array('error'=>0,'data'=>$area->getData()));
    	}
    	catch(Exception $e){
    		//回滚
			$trascation->rollback();
    		$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
}