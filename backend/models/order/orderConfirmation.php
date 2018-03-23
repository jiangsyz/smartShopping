<?php
//订单确认信息处理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderConfirmation extends Component{
	//订单
	public $order=NULL;
	//========================================
	//获取确认信息
	public function getConfirmation(){
		$data=$this->order->getOrderRecordData();
		$data['isEffective']=$this->order->isEffective();
		$data['effectiveChildOrderCount']=$this->order->effectiveChildOrderCount;
		$data['effectiveBuyingRecordCount']=$this->order->effectiveBuyingRecordCount;
		$data['childOrders']=array();
		$data['salesUnits']=array();
		//获取子订单确认信息
		foreach($this->order->childOrders as $childOrder){
			$orderConfirmation=new self(array('order'=>$childOrder));
			$data['childOrders'][]=$orderConfirmation->getConfirmation();
		}
		//获取购物行为确认信息
		foreach($this->order->buyingRecords as $record){
			$salesUnitInfo=array();
			$salesUnitInfo['salesUnitNo']=$record->salesUnit->getSourceNo();
			$salesUnitInfo['salesUnitType']=$record->salesUnit->getSourceType();
			$salesUnitInfo['salesUnitId']=$record->salesUnit->getSourceId();
			$salesUnitInfo['productType']=$record->salesUnit->getProductType();
			$salesUnitInfo['productId']=$record->salesUnit->getProductId();
			$salesUnitInfo['title']=$record->salesUnit->getProductName();
			$salesUnitInfo['viceTitle']=$record->salesUnit->getSalesUnitName();
			$salesUnitInfo['keepCount']=$record->salesUnit->getKeepCount();
			$salesUnitInfo['cover']=$record->salesUnit->getCover();
			$salesUnitInfo['price']=$record->salesUnit->getLevelPrice(0);
			$salesUnitInfo['memberPrice']=$record->salesUnit->getLevelPrice(1);
			$salesUnitInfo['finalPrice']=$record->salesUnit->getFinalPrice($this->order->member);
			$salesUnitInfo['buyCount']=$record->buyCount;
			$salesUnitInfo['totalPrice']=$record->getPrice();
			$salesUnitInfo['totalFinalPrice']=$record->getFinalPrice();
			$salesUnitInfo['isSelected']=$record->isSelected;
			$data['salesUnits'][]=$salesUnitInfo;
		}
		return $data;
	}
}