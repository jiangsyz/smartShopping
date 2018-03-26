<?php
//基于配送方式分单的订单工厂
namespace backend\models\orderFactory;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\distribute\distribute;
use backend\models\member\member;
//========================================
class distributeFactory extends orderFactory{
	//商家
	public $distributeType=NULL;
	//购买行为集合
	private $buyingRecords=array();
	//========================================
	//向工厂添加购买行为
	public function addBuyingRecord(buyingRecord $buyingRecord){
		//在购买行为集合中,销售单元唯一编号是索引,一个销售单元的购买行为只能有一条
		$index=$buyingRecord->salesUnit->getSourceNo();
		if(isset($this->buyingRecords[$index])) throw new SmartException("bRecord {$index} existed");
		//加入销售单元和数量
		$this->buyingRecords[$index]=$buyingRecord;
	}
	//========================================
	//获取非会员价格(不含运费)
	public function getPrice(){
		$price=0;
		//累加选中的购买行为的价格
		foreach($this->buyingRecords as $r) if($r->isSelected) $price+=$r->getPrice();
		return $price;
	}
	//========================================
	//获取会员价(不含运费)
	public function getMemberPrice(){
		$price=0;
		//累加选中的购买行为的价格
		foreach($this->buyingRecords as $r) if($r->isSelected) $price+=$r->getMemberPrice();
		return $price;
	}
	//========================================
	//获取最终成交价格(不含运费)
	public function getFinalPrice(){
		$finalPrice=0;
		//累加选中的购买行为的价格
		foreach($this->buyingRecords as $r) if($r->isSelected) $finalPrice+=$r->getFinalPrice();
		return $finalPrice;
	}
	//========================================
	//获取运费
	public function getFreight(){return 0;}
	//========================================
	//获取订单标题
	public function getTitle(){return $this->distributeType;}
	//========================================
	//获取索引,在整个订单树中唯一
	public function getIndex(){return md5($this->distributeType);}
	//========================================
	//获取订单中购物行为的总重量
	public function getWeight(){
		$total=0;
		foreach($this->buyingRecords as $buyingRecord){
			//没选中的不计算
			if(!$buyingRecord->isSelected) continue;
			//获取重量
			$weight=$buyingRecord->salesUnit->getProperty("weight");
			//计入总计
			if($weight) $total+=$weight*$buyingRecord->buyCount;
		}
	}
	//========================================
	//初始化订单
	public function initOrder(){
		parent::initOrder();
		//加入购物行为
		foreach($this->buyingRecords as $buyingRecord) 
			if($buyingRecord->isSelected) $this->order->addBuyingRecord($buyingRecord);
		return $this->order;
	}
}