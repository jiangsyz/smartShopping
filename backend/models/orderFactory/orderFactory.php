<?php
//订单工厂
namespace backend\models\orderFactory;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\member\member;
use backend\models\order\order;
//========================================
abstract class orderFactory extends Component{
	//会员
	public $member=NULL;
	//父工厂
	public $parentFactory=false;
	//子工厂
	public $childFactories=array();
	//订单
	public $order=false;
	//========================================
	//向工厂添加购买行为
	abstract public function addBuyingRecord(buyingRecord $buyingRecord);
	//获取价格(不含运费)
	abstract public function getPrice();
	//获取最终成交价格(不含运费)
	abstract public function getFinalPrice();
	//获取运费
	abstract public function getFreight();
	//获取标题
	abstract public function getTitle();
	//索引,在整个订单树中唯一
	abstract public function getIndex();
	//========================================
	//获取工厂类型
	public function getFactoryType(){return static::className();}
	//========================================
	//获取订单价格(分为单位)	
	public function getPay(){return (static::getFinalPrice()+static::getFreight())*100;}
	//========================================
	//初始化订单
	public function initOrder(){
		//初始化订单
		$order=array();
		$order['member']=$this->member;
		$order['index']=static::getIndex();
		$order['factoryType']=static::getFactoryType();
		$order['title']=static::getTitle();
		$order['freight']=static::getFreight();
		$order['price']=static::getPrice();
		$order['finalPrice']=static::getFinalPrice();
		$order['pay']=static::getPay();
		$this->order=new order($order);
		//迭代初始化子订单
		foreach($this->childFactories as $f) $this->order->addChildOrder($f->initOrder($member));
		//返回订单
		return $this->order;
	}
}