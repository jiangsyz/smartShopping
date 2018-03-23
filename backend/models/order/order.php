<?php
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\orderFactory\buyingRecord;
use backend\models\member\member;
use backend\models\member\address;
use backend\models\distribute\distribute;
//========================================
class order extends Component{
	//会员
	public $member=NULL;
	//索引,在整个订单树中唯一
	public $index=NULL;
	//工厂类型
	public $factoryType=NULL;
	//标题
	public $title=NULL;
	//运费
	public $freight=NULL;
	//非会员价
	public $price=NULL;
	//会员价
	public $memberPrice=NULL;
	//成交价格
	public $finalPrice=NULL;
	//节省下的金额
	public $reduction=NULL;
	//支付费用(分单位)
	public $pay=NULL;
	//父订单
	public $parentOrder=false;
	//子订单
	public $childOrders=array();
	//购买行为
	public $buyingRecords=array();
	//有效的子订单数
	public $effectiveChildOrderCount=0;
	//有效的购买行为数
	public $effectiveBuyingRecordCount=0;
	//========================================
	//判断是否为有效订单
	public function isEffective(){
		//即没有子订单也没有购物行为的为非有效订单
		$count=$this->effectiveChildOrderCount+$this->effectiveBuyingRecordCount;
		if($count==0) return false; else return true;
	}
	//========================================
	//是否需要收获地址
	public function isNeedAddress(){
		//两个循环只会跑一个,这么写是为了方便
		foreach($this->childOrders as $v) 
			if($v->isNeedAddress()) return true;
		foreach($this->buyingRecords as $v) 
			if($v->salesUnit->getDistributeType()!=distribute::TYPE_VIRTUAL) return true;
		return false;
	}
	//========================================
	//添加子订单(非叶子订单不能有购物行为)
	public function addChildOrder(order $childOrder){
		//每个订单只能有一个父订单
		if($childOrder->parentOrder) throw new SmartException("childOrder had parent");
		//非叶子订单不能有购物行为
		if(!empty($this->buyingRecords)) throw new SmartException("buyingRecords is not empty");
		//添加父订单
		$childOrder->parentOrder=$this;
		//添加子订单
		$this->childOrders[]=$childOrder;
		//有效子订单计数
		if($childOrder->isEffective()) $this->effectiveChildOrderCount++;
	}
	//========================================
	//添加购物行为(只有叶子订单才能有购物行为)
	public function addBuyingRecord(buyingRecord $buyingRecord){
		//只有叶子订单才能有购物行为
		if(!empty($this->childOrders)) throw new SmartException("childOrders is not empty");
		//同一个资源的购物行为不能重复		
		$index=$buyingRecord->salesUnit->getSourceNo();
		if(isset($this->buyingRecords[$index])) throw new SmartException("buyingRecord repeat");
		//添加购物行为
		$this->buyingRecords[$index]=$buyingRecord;
		//有效购物资源计数
		if($buyingRecord->isSelected) $this->effectiveBuyingRecordCount++;
	}
	//========================================
	//获取创建订单记录所需的数据
	public function getOrderRecordData(){
		$data=array();
		$data['memberId']=$this->member->id;
		$data['index']=$this->index;
		$data['factoryType']=$this->factoryType;
		$data['title']=$this->title;
		$data['price']=$this->price;
		$data['memberPrice']=$this->memberPrice;
		$data['reduction']=$this->reduction;
		$data['finalPrice']=$this->finalPrice;
		$data['freight']=$this->freight;
		$data['pay']=$this->pay;
		$data['isNeedAddress']=$this->isNeedAddress();
		$data['parentId']=NULL;
		$data['command']=NULL;
		return $data;
	}
	//========================================
	//创建订单记录
	public function createOrderRecord($command,$parentId=NULL){
		//获取创建订单记录所需的数据
		$data=$this->getOrderRecordData();
		//补充父订单id
		$data['parentId']=$parentId;
		//补充创建订单时所需的外部命令
		$data['command']=$command;
		//生成订单记录
		$orderRecord=orderRecord::addObj($data);
		//处理购买行为
		foreach($this->buyingRecords as $buyingRecord){
			if(!$buyingRecord->isSelected) continue;
			$orderBuyingRecord=array();
			$orderBuyingRecord['orderId']=$orderRecord->id;
			$orderBuyingRecord['sourceType']=$buyingRecord->salesUnit->getSourceType();
			$orderBuyingRecord['sourceId']=$buyingRecord->salesUnit->id;
			$orderBuyingRecord['buyingCount']=$buyingRecord->buyCount;
			$orderBuyingRecord['price']=$buyingRecord->salesUnit->getPrice();
			$orderBuyingRecord['finalPrice']=$buyingRecord->salesUnit->getFinalPrice();
			orderBuyingRecord::addObj($orderBuyingRecord);
		}
		//处理子订单
		foreach($this->childOrders as $child) $child->createOrderRecord($command,$orderRecord->id);
		//返回订单
		return $orderRecord;
	}
}