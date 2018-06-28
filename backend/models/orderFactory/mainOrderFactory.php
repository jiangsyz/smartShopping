<?php
//主订单工厂
namespace backend\models\orderFactory;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\model\shop;
use backend\models\member\member;
use backend\models\distribute\distribute;
//========================================
class mainOrderFactory extends orderFactory{
	//向工厂添加购买行为
	public function addBuyingRecord(buyingRecord $buyingRecord){
		//获取配送方式
		$distributeType=$buyingRecord->salesUnit->getDistributeType();
		//获取子订单工厂
		if(!isset($this->childFactories[$distributeType])){
			$distributeFactoryData=array();
			$distributeFactoryData['member']=$this->member;
			$distributeFactoryData['parentFactory']=$this;
			$distributeFactoryData['distributeType']=$distributeType;
			$this->childFactories[$distributeType]=new distributeFactory($distributeFactoryData);
		}
		//将购物行为加入子订单工厂
		$this->childFactories[$distributeType]->addBuyingRecord($buyingRecord);
	}
	//========================================
	//获取非会员价格(不含运费)
	public function getPrice(){
		$price=0;
		//累加子订单的价格
		foreach($this->childFactories as $f) $price+=$f->getPrice();
		return $price;
	}
	//========================================
	//获取会员价(不含运费)
	public function getMemberPrice(){
		$price=0;
		//累加子订单的价格
		foreach($this->childFactories as $f) $price+=$f->getMemberPrice();
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
		//不是会员
		if($this->member->getLevel()==0){
			//除了单买虚拟产品,其他情况一口价38
			foreach($this->childFactories as $f) 
				if($f->distributeType!=distribute::TYPE_VIRTUAL) return 38;
			return 0;
		}
		//是会员
		else{
			$price=false;
			//如果用户买了非虚拟商品,统计总价
			foreach($this->childFactories as $f)
				if($f->distributeType==distribute::TYPE_VIRTUAL) continue;
				else $price=$price===false?$f->getFinalPrice():$price+$f->getFinalPrice();
			//如果整个订单只有虚拟商品,则没有运费
			if($price===false) return 0;
			//购买了非虚拟商品,则非虚拟商品部分的总价超过300免邮,不满运费18
			else return $price>=300?0:18;
		}
	}
	//========================================
	//获取订单标题
	public function getTitle(){return NULL;}
	//========================================
	//索引,在整个订单树中唯一
	public function getIndex(){return 'main';}
}