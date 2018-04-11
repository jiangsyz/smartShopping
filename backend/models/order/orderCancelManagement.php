<?php
//订单取消管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderCancelManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//取消订单
	public function cancel(){
		//获取状态
		$status=$this->orderRecord->statusManagement->getStatus();
		//处理
		if($status==orderStatusManagement::STATUS_UNPAID) 
			$this->orderRecord->updateObj(array('cancelStatus'=>1));
		elseif($status==orderStatusManagement::STATUS_UNDELIVERED){
			$this->orderRecord->updateObj(array('cancelStatus'=>1));
			$refund=array();
			$refund['bid']=0;
			$refund['price']=$this->orderRecord->pay;
			$refund['applyMemo']="整单取消";
			refund::applyRefund($this->orderRecord,$refund);
		}
		//不允许处理
		else 
			throw new SmartException("该订单状态不能取消",-2);
	}
	//========================================
}