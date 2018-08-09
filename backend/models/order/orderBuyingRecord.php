<?php
//订单购买行为记录
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
use backend\models\model\source;
use backend\models\order\orderRecord;
//========================================
class orderBuyingRecord extends source{
	const EVENT_BUYING_SUCCESS=1;//购买成功后触发的时间
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initLogisticsId"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkBuyingCount"));
		$this->on(self::EVENT_AFTER_INSERT,array($this,"deductKeepCount"));
		$this->on(self::EVENT_BUYING_SUCCESS,array($this,"buyingSuccess"));
	}
	//========================================
	//初始化物流平台
	public function initLogisticsId(){
		$logistics=$this->salesUnit->getLogistics();
		if($logistics) $this->logisticsId=$logistics->id; else $this->logisticsId=NULL;
	}
	//========================================
	//返回资源类型
	public function getSourceType(){return source::TYPE_ORDER_BUYING_RECORD;}
	//========================================
	//获取购买单元
	public function getSalesUnit(){
		return source::getSource($this->sourceType,$this->sourceId,true);
	}
	//========================================
	//获取订单
	public function getOrderRecord(){
		return $this->hasOne(orderRecord::className(),array('id'=>'orderId'));
	}
	//========================================
	//检查购买数量
	public function checkBuyingCount(){
		if($this->buyingCount<1) throw new SmartException("buyingCount < 1");	
	}
	//========================================
	//扣除库存
	public function deductKeepCount(){
		//获取购买单元
		$salesUnit=$this->getSalesUnit();
		if(!$salesUnit) throw new SmartException("miss salesUnit");
		//修改库存
		$handlerType=$this->orderRecord->getSourceType();
		$handlerId=$this->orderRecord->getSourceId();
		$keepCount=$salesUnit->getKeepCount()-$this->buyingCount;
		$salesUnit->updateKeepCount($handlerType,$handlerId,$keepCount,$this->id);
	}
	//========================================
	//返回库存
	public function backKeepCount(){
		//获取购买单元
		$salesUnit=$this->getSalesUnit();
		if(!$salesUnit) throw new SmartException("miss salesUnit");
		//修改库存
		$handlerType=$this->orderRecord->getSourceType();
		$handlerId=$this->orderRecord->getSourceId();
		$keepCount=$salesUnit->getKeepCount()+$this->buyingCount;
		$salesUnit->updateKeepCount($handlerType,$handlerId,$keepCount,$this->id);
	}
	//========================================
	//购买成功
	public function buyingSuccess(){$this->getSalesUnit()->buyingSuccess($this);}
}