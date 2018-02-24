<?php
//普通订单工厂
namespace backend\models\orderFactory;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\model\shop;
use backend\models\member\member;
//========================================
class normalOrderFactory extends orderFactory{
	//向工厂添加购买行为
	public function addBuyingRecord(buyingRecord $buyingRecord){
		//根据目标商品的商家,加入对应的商家子订单工厂
		$this->getShopFactory($buyingRecord->salesUnit->getShop())->addBuyingRecord($buyingRecord);
	}
	//========================================
	//获取价格(不含运费)
	public function getPrice(){
		$price=0;
		//累加子订单的价格
		foreach($this->childFactories as $f) $price+=$f->getPrice();
		return $price;
	}
	//========================================
	//获取最终成交价格(不含运费)
	public function getFinalPrice(){
		$finalPrice=0;
		//累加子订单的成交价格
		foreach($this->childFactories as $f) $finalPrice+=$f->getFinalPrice();
		return $finalPrice;
	}
	//========================================
	//获取运费
	public function getFreight(){
		$freight=0;
		//累加子订单运费
		foreach($this->childFactories as $f) $freight+=$f->getFreight();
		return $freight;
	}
	//========================================
	//获取订单标题
	public function getTitle(){return NULL;}
	//========================================
	//索引,在整个订单树中唯一
	public function getIndex(){return 'main';}
	//========================================
	//获取某个商家订单子工厂
	public function getShopFactory($shop){
		//确定索引
		$index=$shop->getSourceNo();
		//初始化子工厂
		if(!isset($this->childFactories[$index])){
			$childFactory=new normalShopOrderFactory(array('shop'=>$shop,'parentFactory'=>$this));
			$this->childFactories[$index]=$childFactory;
		}
		//返回子工厂
		return $this->childFactories[$index];
	}
}