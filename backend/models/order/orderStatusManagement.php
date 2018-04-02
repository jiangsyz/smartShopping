<?php
//订单状态管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderStatusManagement extends Component{
	const STATUS_UNPAID=1;//待支付状态
	const STATUS_REFUNDING=2;//退款中
	const STATUS_CLOSED=3;//交易关闭
	//========================================
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//获取状态
	public function getStatus(){
		//退款状态(一定要先判断这个状态,可以减少不必要的重复计算)
		if($this->isRefunding()) return self::STATUS_REFUNDING;
		//交易关闭状态
		elseif($this->isClosed()) return self::STATUS_CLOSED;
		//未支付状态
		elseif($this->isUnpaid()) return self::STATUS_UNPAID;
		else throw new SmartException("error order status");
		
		
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
		else return false;
	}
	//========================================
	//判断是否为退款中状态
	public function isRefunding(){return false;}
}