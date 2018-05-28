<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\token\tokenManagement;
use backend\models\order\orderRecord;
use backend\models\order\orderBuyingRecord;
class RefundController extends SmartWebController{
	public $enableCsrfValidation=false;
	//========================================
	//驳回退款
	public function actionApiReject(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=$this->requestPost('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取订单id
			$orderId=$this->requestPost('orderId',0);
			//获取退款记录id
			$refundId=$this->requestPost('refundId',0);
			//获取备注
			$memo=$this->requestPost('memo',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//驳回退款
			$orderRecord->refundManagement->reject($staff,$refundId,$memo);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//重启退款
	public function actionApiReopen(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=$this->requestGet('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取订单id
			$orderId=$this->requestGet('orderId',0);
			//获取退款记录id
			$refundId=$this->requestGet('refundId',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//重开退款
			$orderRecord->refundManagement->reopen($refundId);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//针对单个购物行为的退款
	public function actionApiRefundByBuyingRecord(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=$this->requestPost('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取订单id
			$orderId=$this->requestPost('orderId',0);
			//获取退款记录id
			$buyingRecordId=$this->requestPost('buyingRecordId',0);
			//获取退款价格
			$price=$this->requestPost('price',0);
			//获取备注
			$memo=$this->requestPost('memo',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("订单不存在",2);
			//获取购物行为
			$buyingRecord=orderBuyingRecord::find()->where("`id`='{$buyingRecordId}'")->one();
			if(!$buyingRecord) throw new SmartException("购物行为不存在",2);
			//退款
			$orderRecord->refundManagement->allpyRefundByBuyingRecord($staff,$buyingRecord,$price,$memo);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//同意退款
	public function actionApiRefund(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=$this->requestGet('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取订单id
			$orderId=$this->requestGet('orderId',0);
			//获取退款记录id
			$refundId=$this->requestGet('refundId',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//退款
			$orderRecord->refundManagement->refund($staff,$refundId);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//重置退款
	public function actionApiReset(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=$this->requestGet('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取订单id
			$orderId=$this->requestGet('orderId',0);
			//获取退款记录id
			$refundId=$this->requestGet('refundId',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//重开退款
			$orderRecord->refundManagement->reset($refundId);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
}