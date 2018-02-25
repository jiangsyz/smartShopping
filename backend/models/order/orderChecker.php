<?php
//订单检查器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderChecker extends Component{
	//订单
	public $order=NULL;
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->check();
	}
	//========================================
	//检查
	private function check(){
		$this->checkEffect();
		$this->checkeepCount();
		$this->checkChildOrders();
	}
	//========================================
	//检查订单有效性
	private function checkEffect(){
		if(!$this->order->isEffective()) throw new SmartException("checkEffect return false");
	}
	//========================================
	//检查订单下的购买目标的库存
	private function checkeepCount(){
		foreach($this->order->buyingRecords as $r){
			//获取购买对象库存(已经加锁)
			$keepCount=$r->salesUnit->getKeepCount();
			//库存为NULL,代表没有库存限制
			if($keepCount==NULL) continue;
			//如果购买数量大于库存,报错
			if($r->buyCount>$keepCount) throw new SmartException("need more keepCount");
		}
	}
	//========================================
	//检查子订单有效性
	public function checkChildOrders(){
		foreach($this->order->childOrders as $order) new self(array('order'=>$order));
	}
}