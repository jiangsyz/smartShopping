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
	//获取会员地址簿
	public function actionApiGetAddressList(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取地址簿
			$addressList=$member->getAddressList();
			//组织数据
			$data=array();
			foreach($addressList as $address){
				$addressInfo=$address->getData();
				$addressInfo['areaFullId']=$address->area->full_area_id;
				$addressInfo['areaFullName']=$address->area->full_area_name;
				$data[]=$addressInfo;
			}
			//提交事务
			$trascation->commit();
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(Exception $e){
    		//回滚
			$trascation->rollback();
    		$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
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
			$detail=Yii::$app->request->post('detail',false); 
			if(!$detail) throw new SmartException("miss detail");
			//获取邮编
			$postCode=Yii::$app->request->post('postCode',NULL);
			//添加
			$addressData=array();
			$addressData['memberId']=$member->id;
			$addressData['name']=$name;
			$addressData['phone']=$phone;
			$addressData['areaId']=$areaId;
			$addressData['address']=$detail;
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
	//删除某个会员地址
	public function actionApiDelAddress(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取地址id
			$addressId=Yii::$app->request->get('addressId',0);
			//获取地址
			$where="`id`='{$addressId}' AND `memberId`='{$member->id}'";
			$address=address::find()->where($where)->one();
			if(!$address) throw new SmartException("miss member address");
			//删除地址
			$address->delete();
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
	//编辑某个会员地址
	public function actionApiUpdateAddress(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->post('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取地址id
			$addressId=Yii::$app->request->post('addressId',0);
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
			$detail=Yii::$app->request->post('detail',false); 
			if(!$detail) throw new SmartException("miss detail");
			//获取邮编
			$postCode=Yii::$app->request->post('postCode',NULL);
			//获取地址
			$where="`id`='{$addressId}' AND `memberId`='{$member->id}' AND `isDeled`='0'";
			$address=address::find()->where($where)->one();
			if(!$address) throw new SmartException("miss member address");
			//修改
			$addressData=array();
			$addressData['name']=$name;
			$addressData['phone']=$phone;
			$addressData['areaId']=$areaId;
			$addressData['address']=$detail;
			$addressData['postCode']=$postCode;
			$address->updateObj($addressData);
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
}