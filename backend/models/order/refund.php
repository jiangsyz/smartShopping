<?php
//退款
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class refund extends SmartActiveRecord{
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initData"));
	}
	//========================================
	//初始化数据
	public function initData(){
		$this->applyTime=time();
		$this->status=0;
		$this->rejectMemo="";
		$this->refundMemo="";
	}
	//========================================
	//申请一个退款
	public static function applyRefund(orderRecord $orderRecord,$data){
		//订单没付过钱的不能退
		if($orderRecord->payStatus!=1) throw new SmartException("未支付订单不能申请退款",-2);
		//校验
		if(!isset($data['bid'])) throw new SmartException("miss bid");
		if(!isset($data['price'])) throw new SmartException("miss price");
		if(!isset($data['applyMemo'])) throw new SmartException("miss applyMemo");
		//添加退款记录
		$refundData=array();
		$refundData['oid']=$orderRecord->id;
		$refundData['bid']=$data['bid'];
		$refundData['price']=$data['price'];
		$refundData['applyMemo']=$data['applyMemo'];
		self::addObj($refundData);
		//修改订单相关数据
		$orderRecord->updateObj(array('refundingStatus'=>1,'finishStatus'=>0));
	}
}