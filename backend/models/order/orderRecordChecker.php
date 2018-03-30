<?php
//订单记录检查器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderRecordChecker extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//检查
	public function check(){
		//订单
		$oRecord=$this->orderRecord;
		//检查订单关系
		$this->checkRelation();
		//检查收获地址
		$this->checkAddress();
		//检查预期收货日期
		$this->checkDate();
		//递归检查子订单
		foreach($oRecord->relationManagement->getChildren() as $c) $c->checker->check();
	}
	//========================================
	//检查地址
	public function checkAddress(){
		//订单
		$oRecord=$this->orderRecord;
		//不需要送货地址
		if(!$oRecord->isNeedAddress) return;
		//获取送货地址
		$address=$oRecord->addressManagement->getAddress();
		//获取不到送货地址报错
		if(!$address) throw new SmartException("order {$oRecord->id} miss address");
	}
	//========================================
	//检查订单关系
	public function checkRelation(){
		//订单
		$oRecord=$this->orderRecord;
		//如果有父订单id,父订单要存在
		if($oRecord->parentId)
			if(!$oRecord->relationManagement->getParent())
				throw new SmartException("order {$oRecord->id} miss parent");
		//获取子订单
		$children=$oRecord->relationManagement->getChildren();
		//获取购买行为
		$buyingRecords=$oRecord->buyingManagement->getBuyingRecords();
		//不能即没子订单也没购买行为
		if(!$children && !$buyingRecords) 
			throw new SmartException("order {$oRecord->id} effective error1");
		//不能既有子订单又有购买行为
		if($children && $buyingRecords)
			throw new SmartException("order {$oRecord->id} effective error2");
	}
	//========================================
	//检查预期收货日期
	public function checkDate(){
		//订单
		$oRecord=$this->orderRecord;
		//不需要送货地址
		if(!$oRecord->isNeedAddress) return;
		//预期收货日期
		$date=$oRecord->dateManagement->getDate();
		//获取不到预期收货日期
		if(!$date) throw new SmartException("order {$oRecord->id} miss date");
		//预期收货日期必须是1/2/4
		if(!in_array($date['val'],array(1,2,4))) 
		throw new SmartException("order {$oRecord->id} error date");
	}
}