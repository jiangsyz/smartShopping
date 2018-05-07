<?php
//订单退款管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\model\source;
use backend\models\order\refund;
//========================================
class orderRefundManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//获取该订单有效退款记录(剔除驳回的)
	public function getActiveRefunds(){
		$tableName=refund::tableName();
		$sql="SELECT * FROM {$tableName} WHERE `oid`='{$this->orderRecord->id}' AND `status`<>'-1' FOR UPDATE";
		return refund::findBySql($sql)->all();
	}
	//========================================
	//申请针对整单的退款
	public function allpyRefundByOrder(source $handler,$price,$memo){
		//未支付订单不能申请退款
		if($this->orderRecord->payStatus!=1) throw new SmartException("未支付订单不能申请退款",-2);
		//不能有重复的退款记录
		if($this->getActiveRefunds()) throw new SmartException("重复退款",-2);
		//只有关闭状态能整单退
		$orderStatus=$this->orderRecord->statusManagement->getStatus();
		if($orderStatus!=orderStatusManagement::STATUS_CLOSED) throw new SmartException("订单状态错误",-2);
		//必须要备注
		if(!$memo) throw new SmartException("必须要备注",-2);
		//添加退款记录
		$refundData=array();
		$refundData['oid']=$this->orderRecord->id;
		$refundData['bid']=0;
		$refundData['price']=$price;
		$refundData['applyHandlerType']=$handler->getSourceType();
		$refundData['applyHandlerId']=$handler->getSourceId();
		$refundData['applyMemo']=$memo;
		refund::addObj($refundData);
		//修改订单相关数据
		$this->orderRecord->updateObj(array('refundingStatus'=>1,'finishStatus'=>0));
	}
	//========================================
	//申请针对单个购买目标退款
	public function allpyRefundByBuyingRecord(source $handler,orderBuyingRecord $orderBuyingRecord,$price,$memo){
		//未支付订单不能申请退款
		if($this->orderRecord->payStatus!=1) throw new SmartException("未支付订单不能申请退款",-2);
		//不能有重复的退款记录
		$activeRefunds=$this->getActiveRefunds();
		foreach($activeRefunds as $activeRefund){
			if($activeRefund->bid==0) throw new SmartException("重复退款",-2);
			if($activeRefund->bid==$orderBuyingRecord->id) throw new SmartException("重复退款",-2);
		}
		//只有在退款中/待收货/已完成状态能单个退
		$orderStatus=$this->orderRecord->statusManagement->getStatus();
		$allowStatusList=array();
		$allowStatusList[]=orderStatusManagement::STATUS_REFUNDING;
		$allowStatusList[]=orderStatusManagement::STATUS_UNRECEIPTED;
		$allowStatusList[]=orderStatusManagement::STATUS_FINISHED;
		if(!in_array($orderStatus,$allowStatusList)) throw new SmartException("订单状态错误",-2);
		//必须要备注
		if(!$memo) throw new SmartException("必须要备注",-2);
		//添加退款记录
		$refundData=array();
		$refundData['oid']=$this->orderRecord->id;
		$refundData['bid']=$orderBuyingRecord->id;
		$refundData['price']=$price;
		$refundData['applyHandlerType']=$handler->getSourceType();
		$refundData['applyHandlerId']=$handler->getSourceId();
		$refundData['applyMemo']=$memo;
		refund::addObj($refundData);
		//修改订单相关数据
		$this->orderRecord->updateObj(array('refundingStatus'=>1,'finishStatus'=>0));
	}
	//========================================
	//驳回
	public function reject(source $handler,$refundId,$memo){
		//获取退款记录
		$tableName=refund::tableName();
		$refund=refund::findBySql("SELECT * FROM {$tableName} WHERE `id`='{$refundId}' FOR UPDATE")->one();
		//找不到
		if(!$refund) throw new SmartException("找不到该退款记录",-2);
		//订单不对
		if($refund->oid!=$this->orderRecord->id) throw new SmartException("订单关系错误",-2);
		//状态错误
		if($refund->status!=0) throw new SmartException("退款记录状态错误",-2);
		//必须要备注
		if(!$memo) throw new SmartException("必须要备注",-2);
		//驳回
		$update=array();
		$update['rejectHandlerType']=$handler->getSourceType();
		$update['rejectHandlerId']=$handler->getSourceId();
		$update['rejectTime']=time();
		$update['rejectMemo']=$memo;
		$update['status']=-1;
		$refund->updateObj($update);
		//如果驳回后订单没有进行中的退款,将订单的退款中标示位置为不在退款中
		if(!$this->getActiveRefunds()) $this->orderRecord->updateObj(array('refundingStatus'=>0));
	}
}