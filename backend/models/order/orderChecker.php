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
		$this->checkEffect();//检查订单有效性
		$this->checkeepCount();//检查订单下的购买目标的库存
		$this->checkIsAllowSale();//检查订单下的购买目标是否允许购买
		$this->checkChildOrders();//检查子订单有效性
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
			//获取购买对象
			$salesUnit=$r->salesUnit;
			//获取购买对象全局编号
			$salesUnitNo=$salesUnit->getSourceNo();
			//获取购买对象库存
			$keepCount=$salesUnit->getKeepCount();
			//库存为NULL,代表没有库存限制
			if($keepCount===NULL) continue;
			//如果购买数量大于库存,报错
			if($r->buyCount>$keepCount) throw new SmartException("{$salesUnitNo} need more keepCount");
		}
	}
	//========================================
	//检查订单下的购买目标是否允许购买
	private function checkIsAllowSale(){
		foreach($this->order->buyingRecords as $r){
			//获取购买对象
			$salesUnit=$r->salesUnit;
			//获取购买对象全局编号
			$salesUnitNo=$salesUnit->getSourceNo();
			//如果购买对象不允许销售,报错
			if(!$salesUnit->isAllowSale()) throw new SmartException("{$salesUnitNo} is not allow sale");
		}
	}
	//========================================
	//检查子订单有效性
	public function checkChildOrders(){
		foreach($this->order->childOrders as $order) new self(array('order'=>$order));
	}
}