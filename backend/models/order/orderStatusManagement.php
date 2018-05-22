<?php
//订单状态管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\notice\notice;
//========================================
class orderStatusManagement extends Component{
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
	const EVENT_STATUS_CHANGED=1;//状态更改事件
	//========================================
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_STATUS_CHANGED,array($this,"getStatus"));
	}
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
		$r=$this->orderRecord;
		//不是主订单不处理
		if($r->parentId) return;
		//不是未支付不处理
		if($r->payStatus!=0) return;
		//取消或关闭的不处理
		if($r->cancelStatus==1) return;
		if($r->closeStatus==1) return;
		//计算等待支付时常
		$waiting=time()-$r->createTime;
		//超时了修改支付状态
		if($waiting>Yii::$app->params["payTimeOut"]) $r->updateObj(array('payStatus'=>-1));
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
	//========================================
	//发货
	public function delivered(){
		$r=$this->orderRecord;
		//获取状态
		$status=$this->getStatus();
		//不是待收货和退款中状态不处理
		if(!($status==self::STATUS_UNRECEIPTED || $status==self::STATUS_REFUNDING)) 
			throw new SmartException("error status");
		//不是已配货不处理
		if($r->deliverStatus!=1) 
			throw new SmartException("error deliverStatus");
		//获取所有购买行为
		$buyingRecords=$r->buyingManagement->getBuyingList();
		//如果任意一条购买行为有物流单号,则进行发货处理
		foreach($buyingRecords as $b){
			if($b->logisticsCode){
				//将deliverStatus改为已发货
				$r->updateObj(array('deliverStatus'=>2));
				//发送通知
				$orderShowId=$r->extraction->getShowId();
		        $notice=array();
		        $notice['memberId']=$r->memberId;
		        $notice['type']=notice::TYPE_ORDER;
		        $notice['content']="您的订单{$orderShowId}已经发货,请注意查收!";
		        notice::addObj($notice);
		        //结束处理
		        return;
			}
		}
	}
	//========================================
	//退款
	public function refunding(){
		if($this->orderRecord->refundingStatus==1) return;
		$this->orderRecord->updateObj(array('refundingStatus'=>1,'finishStatus'=>0));
	}
	//========================================
	//退款完成
	public function refunded(){
		if($this->orderRecord->refundingStatus!=1) return;
		$this->orderRecord->updateObj(array('refundingStatus'=>0));
		//退款状态去除后,一些订单应该修改finishStatus,getStatus()会完成这一操作
		$this->getStatus();
	}
}