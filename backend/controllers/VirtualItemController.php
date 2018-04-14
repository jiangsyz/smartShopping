<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\product\virtualItem;
use backend\models\token\tokenManagement;
use backend\models\model\source;
class VirtualItemController extends SmartWebController{
	//获取所有虚拟产品
	public function actionApiGetVirtualItem(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取虚拟产品
			$virtualItems=virtualItem::find()->where("`closed`='0' AND `locked`='0'")->all();
			//组织数据
			$data=array();
			foreach($virtualItems as $v) $data[]=$v->getExtraction()->getBasicData($member);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
}