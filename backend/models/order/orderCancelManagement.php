<?php
//订单取消管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use yii\base\models\source;
use backend\models\staff\staff;
//========================================
class orderCancelManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//取消订单
	public function cancel(){
		//获取状态
		$status=$this->orderRecord->statusManagement->getStatus();
		//未支付订单直接标记取消即可
		if($status==orderStatusManagement::STATUS_UNPAID) 
			$this->orderRecord->updateObj(array('cancelStatus'=>1));
		//待发货订单先标记取消后申请整单退款
		elseif($status==orderStatusManagement::STATUS_UNDELIVERED){
			$this->orderRecord->updateObj(array('cancelStatus'=>1));
			$refundManagement=$this->orderRecord->refundManagement;
			$refundManagement->allpyRefundByOrder($this->orderRecord->member,$this->orderRecord->pay,"会员整单取消");
		}
		//不允许处理
		else 
			throw new SmartException("该订单状态不能取消",-2);
	}
	//========================================
	//订单关闭
	public function close(staff $staff){
		//获取状态
		$status=$this->orderRecord->statusManagement->getStatus();
		//未支付订单直接标记取消即可
		if($status==orderStatusManagement::STATUS_UNPAID) 
			$this->orderRecord->updateObj(array('closeStatus'=>1));
		//待发货订单先标记取消后申请整单退款
		elseif($status==orderStatusManagement::STATUS_UNDELIVERED){
			$this->orderRecord->updateObj(array('closeStatus'=>1));
			$refundManagement=$this->orderRecord->refundManagement;
			$refundManagement->allpyRefundByOrder($staff,$this->orderRecord->pay,"员工整单关闭");
		}
		//不允许处理
		else 
			throw new SmartException("该订单状态不能关闭",-2);
	}
	//========================================
	//返回库存
	public function backKeepCount(){
		$orderRecord=$this->orderRecord;
		//只处理主订单
		if($orderRecord->parentId) 
			throw new SmartException("该订单不是主订单",-2);
		//只处理支付失败/取消/关闭的订单
		if(!($orderRecord->payStatus==-1 || $orderRecord->cancelStatus==1 || $orderRecord->closeStatus==1))
			throw new SmartException("该订单状态不允许返回库存",-2);
		//不重复处理
		if($orderRecord->backKeepCountStatus!=0) 
			throw new SmartException("重复处理",-2);
		//获取购买记录
		$buyingRecords=$orderRecord->buyingManagement->getBuyingList();
		//返回库存
		foreach($buyingRecords as $buyingRecord) $buyingRecord->backKeepCount();
		//标记已返还
		$orderRecord->updateObj(array('backKeepCountStatus'=>1));
	}
	//========================================
}