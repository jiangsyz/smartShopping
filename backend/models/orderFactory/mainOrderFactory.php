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
			foreach($this->childFactories as $v) 
				if($v->distributeType!=distribute::TYPE_VIRTUAL) return 38;
			return 0;
		}
		//是会员
		else{
			//如果冷链不足2公斤收38,其他情况包邮
			foreach($this->childFactories as $v)
				if($v->distributeType==distribute::TYPE_REFRIGERATION) if($v->getWeight()<2000) return 38;
			return 0;
		}
	}
	//========================================
	//获取订单标题
	public function getTitle(){return NULL;}
	//========================================
	//索引,在整个订单树中唯一
	public function getIndex(){return 'main';}
}