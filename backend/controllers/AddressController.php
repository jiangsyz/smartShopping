<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\member\address;
use backend\models\token\tokenManagement;
use backend\models\model\source;
class AddressController extends SmartWebController{
	public $enableCsrfValidation=false;
	//========================================
	//会员添加收货地址
	public function actionApiAddAddress(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->post('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取收件人姓名
			$name=Yii::$app->request->post('name',false); 
			if(!$name) throw new SmartException("miss name");
			//获取收件人手机
			$phone=Yii::$app->request->post('phone',false); 
			if(!$phone) throw new SmartException("miss phone");
			//获取地域id
			$areaId=Yii::$app->request->post('areaId',false); 
			if(!$areaId) throw new SmartException("miss areaId");
			//获取详细地址
			$address=Yii::$app->request->post('address',false); 
			if(!$address) throw new SmartException("miss address");
			//获取邮编
			$postCode=Yii::$app->request->post('postCode',NULL);
			//添加
			$addressData=array();
			$addressData['memberId']=$member->id;
			$addressData['name']=$name;
			$addressData['phone']=$phone;
			$addressData['areaId']=$areaId;
			$addressData['address']=$address;
			$addressData['postCode']=$postCode;
			address::addObj($addressData);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0));
    	}
    	catch(Exception $e){
    		//回滚
			$trascation->rollback();
    		$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	
}