<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\token\tokenManagement;
use backend\models\order\orderRecord;
class RefundController extends SmartWebController{
	public $enableCsrfValidation=false;
	//========================================
	//驳回退款
	public function actionApiReject(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=Yii::$app->request->post('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取订单id
			$orderId=Yii::$app->request->post('orderId',0);
			//获取退款记录id
			$refundId=Yii::$app->request->post('refundId',0);
			//获取备注
			$memo=Yii::$app->request->post('memo',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//取消订单
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
			$token=Yii::$app->request->get('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取订单id
			$orderId=Yii::$app->request->get('orderId',0);
			//获取退款记录id
			$refundId=Yii::$app->request->get('refundId',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//取消订单
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
}