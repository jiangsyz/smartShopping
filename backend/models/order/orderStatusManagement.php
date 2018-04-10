<?php
//订单状态管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderStatusManagement extends Component{
	const PAY_TIME_OUT=60*5;//支付超时秒数
	//========================================
	const STATUS_UNPAID=1;//待支付
	const STATUS_REFUNDING=2;//退款中
	const STATUS_CLOSED=3;//交易关闭
	const STATUS_UNDELIVERED=4;//待发货
	const STATUS_UNRECEIPTED=5;//待收货
	const STATUS_FINISHED=6;//已完成
	const STATUS_ERROR1=-1;//异常
	const STATUS_ERROR2=-2;//异常
	const STATUS_ERROR3=-3;//异常
	const STATUS_ERROR4=-4;//异常
	const STATUS_ERROR5=-5;//异常
	const STATUS_ERROR6=-6;//异常
	const STATUS_ERROR7=-7;//异常
	//========================================
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//获取订单状态
	public function getStatus(){
		//检查支付状态
		$this->checkPayTimeout();
		//检查状态,检查错误返回错误状态
		$result=$this->checkStatus(); if($result!==true) return $result;
		//退款中
		if($this->orderRecord->refundingStatus==1) return self::STATUS_REFUNDING;
		//交易关闭
		elseif($this->orderRecord->closeStatus==1) return self::STATUS_CLOSED;
		elseif($this->orderRecord->cancelStatus==1) return self::STATUS_CLOSED;
		elseif($this->orderRecord->payStatus==-1) return self::STATUS_CLOSED;
		//已完成
		elseif($this->orderRecord->finishStatus==1) return self::STATUS_FINISHED;
		elseif($this->orderRecord->deliverStatus==3){
			$this->orderRecord->updateObj(array('finishStatus'=>1));
			return self::STATUS_FINISHED;
		}
		//待收货
		elseif($this->orderRecord->deliverStatus==1) return self::STATUS_UNRECEIPTED;
		elseif($this->orderRecord->deliverStatus==2) return self::STATUS_UNRECEIPTED;
		//待支付或待发货
		elseif($this->orderRecord->deliverStatus==0){
			//待支付
			if($this->orderRecord->payStatus==0) return self::STATUS_UNPAID;
			//待发货
			if($this->orderRecord->payStatus==1){
				//非虚拟订单,待发货
				if($this->orderRecord->isNeedAddress) return self::STATUS_UNDELIVERED;
				//虚拟订单,已完成
				else{
					$this->orderRecord->updateObj(array('deliverStatus'=>3,'finishStatus'=>1));
					return self::STATUS_FINISHED;
				}
			}
		}
		//错误状态
		else throw new SmartException("error order status");
	}
	//========================================
	//检查支付是否超时
	public function checkPayTimeout(){
		//不是主订单不处理
		if($this->orderRecord->parentId) return;
		//不是未支付不处理
		if($this->orderRecord->payStatus!=0) return;
		//取消或关闭的不处理
		if($this->orderRecord->cancelStatus==1) return;
		if($this->orderRecord->closeStatus==1) return;
		//计算等待支付时常
		$waiting=time()-$this->orderRecord->createTime;
		//超时了修改支付状态
		if($waiting>self::PAY_TIME_OUT) $this->orderRecord->updateObj(array('payStatus'=>-1));
	}
	//========================================
	//检查状态
	public function checkStatus(){
		$r=$this->orderRecord;
		//只有主订单才有状态的概念
		if($r->parentId) 
			return self::STATUS_ERROR1;
		//不能共存的指标
		if($r->finishStatus==1 && $r->refundingStatus==1) 
			return self::STATUS_ERROR2;
		if($r->finishStatus==1 && $r->closeStatus==1) 
			return self::STATUS_ERROR3;
		if($r->finishStatus==1 && $r->cancelStatus==1) 
			return self::STATUS_ERROR4;
		if($r->finishStatus==1 && $r->payStatus==－1) 
			return self::STATUS_ERROR5;
		if($r->finishStatus==1 && $r->deliverStatus!=3) 
			return self::STATUS_ERROR6;
		if($r->deliverStatus>0 && $r->payStatus!=1) 
			return self::STATUS_ERROR7;
		//没有错误
		return true;
	}
	//========================================
	//取消订单
	public function cancel(){
		//获取状态
		$status=$this->getStatus();
		//处理
		if($status==self::STATUS_UNPAID) 
			$this->orderRecord->updateObj(array('cancelStatus'=>1));
		elseif($status==self::STATUS_UNDELIVERED) 
			$this->orderRecord->updateObj(array('cancelStatus'=>1));
		//不允许处理
		else 
			throw new SmartException("error status");
	}
	//========================================
	//确认收货
	public function receipted(){
		//获取状态
		$status=$this->getStatus();
		//处理
		if($status==self::STATUS_UNRECEIPTED) 
			$this->orderRecord->updateObj(array('deliverStatus'=>3));
		//不允许处理
		else 
			throw new SmartException("error status");
	}
}