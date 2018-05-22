<?php
//订单退款管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\model\source;
use backend\models\order\refund;
use backend\models\pay\payCallback;
//========================================
class orderRefundManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//退款池
	public $refunds=false;
	//========================================
	//初始化退款池
	public function initRefunds(){
		//只初始化一次
		if($this->refunds!==false) return;
		//加锁获取某个订单的所有退款记录
		$this->refunds=refund::getRefundsOfOrder($this->orderRecord->id);;
	}
	//========================================
	//获取退款池
	public function getRefunds(){
		$this->initRefunds();
		return $this->refunds;
	}
	//========================================
	//获取有效的退款(剔除驳回的)
	public function getActiveRefunds(){
		$refunds=array();
		foreach($this->getRefunds() as $refund) 
			if($refund->status!=refund::STATUS_REJECT) $refunds[]=$refund;
		return $refunds;
	}
	//========================================
	//按购买行为id作为线索获取有效的退款(剔除驳回的)
	public function getActiveRefundsByBid(){
		$refunds=array();
		foreach($this->getActiveRefunds() as $refund) $refunds[$refund->bid][]=$refund;
		return $refunds;
	}
	//========================================
	//获取总退款金额
	public function getTotalPrice(){
		$totalPrice=0;
		foreach($this->getActiveRefunds() as $refund) $totalPrice+=$refund->price;
		return $totalPrice;
	}
	//========================================
	//刷新
	public function flushRefunds(){$this->refunds=false;}
	//========================================
	//检查
	public function checkRefunds(){
		//刷新
		$this->flushRefunds();
		//未支付订单不能涉及退款相关操作
		if($this->orderRecord->payStatus!=1) throw new SmartException("未支付订单不能涉及退款相关操作",-2);
		//退款总额不能超过订单支付总金额
		if($this->getTotalPrice()>$this->orderRecord->pay) throw new SmartException("退款总额大于订单支付金额",-2);
		//获取有效的退款
		$activeRefunds=$this->getActiveRefundsByBid();
		//校验互斥
		foreach($activeRefunds as $bid => $v){
			//每个bid只允许有一个有效退款
			if(count($v)!=1) throw new SmartException("bid重复",-2);
			//整单退款和单个购物行为退款互斥
			if($bid!="0" && isset($refunds["0"])) throw new SmartException("bid互斥",-2);
		}
		//修改订单相关数据
		if(!empty($activeRefunds)) 
			$this->orderRecord->updateObj(array('refundingStatus'=>1,'finishStatus'=>0));
		else
			$this->orderRecord->updateObj(array('refundingStatus'=>0));
	}
	//========================================
	//申请针对整单的退款
	public function allpyRefundByOrder(source $handler,$price,$memo){
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
		//检查订单的退款情况
		$this->checkRefunds();
	}
	//========================================
	//申请针对单个购买目标退款
	public function allpyRefundByBuyingRecord(source $handler,orderBuyingRecord $orderBuyingRecord,$price,$memo){
		//只有在退款中/待收货/已完成状态能单个退
		$orderStatus=$this->orderRecord->statusManagement->getStatus();
		$allowStatusList=array();
		$allowStatusList[]=orderStatusManagement::STATUS_REFUNDING;
		$allowStatusList[]=orderStatusManagement::STATUS_UNRECEIPTED;
		$allowStatusList[]=orderStatusManagement::STATUS_FINISHED;
		if(!in_array($orderStatus,$allowStatusList)) throw new SmartException("订单状态错误",-2);
		//判断购物行为是否属于订单
		$isMyBuyingRecord=$this->orderRecord->buyingManagement->isMyBuyingRecord($orderBuyingRecord);
		if(!$isMyBuyingRecord)throw new SmartException("购买行为不属于该订单",-2);
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
		//检查订单的退款情况
		$this->checkRefunds();
	}
	//========================================
	//驳回
	public function reject(source $handler,$refundId,$memo){
		//获取退款记录
		$refund=refund::getRefund($refundId); if(!$refund) throw new SmartException("找不到该退款记录",-2);
		//订单不对
		if($refund->oid!=$this->orderRecord->id) throw new SmartException("订单关系错误",-2);
		//状态错误
		if($refund->status!=refund::STATUS_TODO) throw new SmartException("退款记录状态错误",-2);
		//必须要备注
		if(!$memo) throw new SmartException("必须要备注",-2);
		//驳回
		$update=array();
		$update['rejectHandlerType']=$handler->getSourceType();
		$update['rejectHandlerId']=$handler->getSourceId();
		$update['rejectTime']=time();
		$update['rejectMemo']=$memo;
		$update['status']=refund::STATUS_REJECT;
		$refund->updateObj($update);
		//检查订单的退款情况
		$this->checkRefunds();
	}
	//========================================
	//重开
	public function reopen($refundId){
		//获取退款记录
		$refund=refund::getRefund($refundId); if(!$refund) throw new SmartException("找不到该退款记录",-2);
		//订单不对
		if($refund->oid!=$this->orderRecord->id) throw new SmartException("订单关系错误",-2);
		//状态错误
		if($refund->status!=refund::STATUS_REJECT) throw new SmartException("退款记录状态错误",-2);
		//重开
		$update=array();
		$update['rejectHandlerType']=NULL;
		$update['rejectHandlerId']=NULL;
		$update['rejectTime']=NULL;
		$update['rejectMemo']=NULL;
		$update['status']=refund::STATUS_TODO;
		$refund->updateObj($update);
		//检查订单的退款情况
		$this->checkRefunds();
	}
	//========================================
	//退款
	public function refund(source $handler,$refundId){
		//获取退款记录
		$refund=refund::getRefund($refundId); if(!$refund) throw new SmartException("找不到该退款记录",-2);
		//订单不对
		if($refund->oid!=$this->orderRecord->id) throw new SmartException("订单关系错误",-2);
		//状态错误
		if($refund->status!=refund::STATUS_TODO) throw new SmartException("退款记录状态错误",-2);
		//修改退款
		$refund->updateObj(array('status'=>refund::STATUS_REFUNDING));
		//增加退款交易
		$refundTransaction=array();
		$refundTransaction['transactionType']='wechat';
		$refundTransaction['transactionId']=Yii::$app->controller->runningId;
		$refundTransaction['refundId']=$refund->id;
		$refundTransaction['transactionHandlerType']=$handler->getSourceType();
		$refundTransaction['transactionHandlerId']=$handler->getSourceId();
		//检查退款池
		$this->checkRefunds();
		//查找支付回调
		$callback=$this->orderRecord->payManagement->getTransactionCallack();
		//根据不同的渠道
		if($callback->payType=="wechat") 
			$this->wechatRefund($callback,$refund->price);
		else 
			throw new SmartException("error payType");
	}
	//========================================
	//微信退款
	public function wechatRefund(payCallback $callback,$refundPrice){
		//校验支付方式
		if($callback->payType!="wechat") throw new SmartException("error payType");
		//解码回调信息
		$callbackData=json_decode($callback->callBackData,true);
		if(!isset($callbackData['attach'])) throw new SmartException("miss attach");
		if(!isset($callbackData['total_fee'])) throw new SmartException("miss total_fee");
		if(!isset($callbackData['transaction_id'])) throw new SmartException("miss transaction_id");
		if(!isset($callbackData['mch_id'])) throw new SmartException("miss mch_id");
		if(!isset($callbackData['appid'])) throw new SmartException("miss appid");
		//完善指令集
		$command=array();
		$command['appid']=$callbackData['appid'];
		$command['mch_id']=$callbackData['mch_id'];
		$command['transaction_id']=$callbackData['transaction_id'];
		$command['out_refund_no']=Yii::$app->controller->runningId;
		$command['total_fee']=$callbackData['total_fee'];
		$command['refund_fee']=$refundPrice;
		//返回调用支付所需的数据
        return Yii::$app->smartWechatPay->refund($command);
	}
}