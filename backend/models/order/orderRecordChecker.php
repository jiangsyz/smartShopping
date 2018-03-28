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
		//根据是否是主订单做不同的检查
		if(!$oRecord->parentId) $this->checkMainOrder(); else $this->checkChildOrder();
		//检查收获地址
		$this->checkAddress();
		//检查预期收货日期
		$this->checkDate();
		//递归检查子订单
		foreach($oRecord->getChildOrders() as $c) $c->checker->check();
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
	//检查主订单
	public function checkMainOrder(){
		//订单
		$oRecord=$this->orderRecord;
		//主订单不能有parentId
		if($oRecord->parentId) throw new SmartException("order {$oRecord->id} error parentId");
		//主订单必须有子订单
		$childOrders=$oRecord->getChildOrders();
		if(!$childOrders) throw new SmartException("order {$oRecord->id} miss childOrders");
		//主订单一定不能有购买行为
		$buyingRecords=$oRecord->getBuyingRecords();
		if($buyingRecords) throw new SmartException("order {$oRecord->id} has buyingRecords");
	}
	//========================================
	//检查子订单
	public function checkChildOrder(){
		//订单
		$oRecord=$this->orderRecord;
		//子订单一定要有父订单
		$parentOrder=$oRecord->getParentOrder();
		if(!$parentOrder) throw new SmartException("order {$oRecord->id} miss parentOrder");
		//获取子订单
		$childOrders=$oRecord->getChildOrders();
		//获取购买行为
		$buyingRecords=$oRecord->getBuyingRecords();
		//子订单和购买行为不能都为空
		$empty=empty($childOrders) && empty($buyingRecords);
		if($empty) throw new SmartException("order {$oRecord->id} empty");
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