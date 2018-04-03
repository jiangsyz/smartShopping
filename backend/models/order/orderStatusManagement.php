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
	const STATUS_UNPAID=1;//待支付状态
	const STATUS_REFUNDING=2;//退款中状态
	const STATUS_CLOSED=3;//交易关闭状态
	const STATUS_UNDELIVERED=4;//待发货
	//========================================
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//检查支付是否超时
	public function checkPayTimeout(){
		//只有主订单才有状态的概念
		if($this->orderRecord->parentId) throw new SmartException("is not main order");
		//只检查支付状态为待支付的订单
		if($this->orderRecord->payStatus!=0) return;
		//计算等待支付时常
		$waiting=time()-$this->orderRecord->createTime;
		//超时了修改支付状态,不能被支付
		if($waiting>self::PAY_TIME_OUT)
			$this->orderRecord->updateObj(array('payStatus'=>-1));
	}
	//========================================
	//获取状态
	public function getStatus(){
		//只有主订单才有状态的概念
		if($this->orderRecord->parentId) throw new SmartException("is not main order");
		//检查支付是否超时
		$this->checkPayTimeout();
		//退款状态(一定要先判断这个状态,可以减少不必要的重复计算)
		if($this->isRefunding()) 
			return self::STATUS_REFUNDING;
		//交易关闭状态
		elseif($this->isClosed()) 
			return self::STATUS_CLOSED;
		//未支付状态
		elseif($this->isUnpaid()) 
			return self::STATUS_UNPAID;
		//待发货状态
		elseif($this->isUndelivered()) 
			return self::STATUS_UNDELIVERED;
		else 
			throw new SmartException("error order status");
	}
	//========================================
	//判断是否为待支付状态
	public function isUnpaid(){
		if($this->isClosed()) return false;
		elseif($this->orderRecord->payStatus==0) return true;
		else return false;
	}
	//========================================
	//判断是否为交易关闭状态
	public function isClosed(){
		if($this->isRefunding()) return false;
		elseif($this->orderRecord->cancelStatus==1) return true;
		elseif($this->orderRecord->closeStatus==1) return true;
		elseif($this->orderRecord->payStatus==-1) return true;
		else return false;
	}
	//========================================
	//判断是否为退款中状态
	public function isRefunding(){return false;}
	//========================================
	//判断是否为待发货状态
	public function isUndelivered(){
		if($this->orderRecord->payStatus==1) return true; else return false;
	}
}