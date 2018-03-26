<?php
//订单确认信息处理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\product\formatPrice;
//========================================
class orderConfirmation extends Component{
	//订单
	public $order=NULL;
	//========================================
	//获取确认信息
	public function getConfirmation(){
		$data=$this->order->getOrderRecordData();
		$data['price']=formatPrice::formatPrice($data['price']);
		$data['memberPrice']=formatPrice::formatPrice($data['memberPrice']);
		$data['reduction']=formatPrice::formatPrice($data['reduction']);
		$data['pay']=formatPrice::formatPrice($data['pay']/100);
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
			$salesUnit=$record->salesUnit;
			$salesUnitInfo['salesUnitNo']=$salesUnit->getSourceNo();
			$salesUnitInfo['salesUnitType']=$salesUnit->getSourceType();
			$salesUnitInfo['salesUnitId']=$salesUnit->getSourceId();
			$salesUnitInfo['productType']=$salesUnit->getProductType();
			$salesUnitInfo['productId']=$salesUnit->getProductId();
			$salesUnitInfo['title']=$salesUnit->getProductName();
			$salesUnitInfo['viceTitle']=$salesUnit->getSalesUnitName();
			$salesUnitInfo['keepCount']=$salesUnit->getKeepCount();
			$salesUnitInfo['cover']=$salesUnit->getCover();
			$salesUnitInfo['price']=formatPrice::formatPrice($salesUnit->getLevelPrice(0));
			$salesUnitInfo['memberPrice']=formatPrice::formatPrice($salesUnit->getLevelPrice(1));
			$salesUnitInfo['finalPrice']=$salesUnit->getFinalPrice($this->order->member);
			$salesUnitInfo['buyCount']=$record->buyCount;
			$salesUnitInfo['totalPrice']=$record->getPrice();
			$salesUnitInfo['totalFinalPrice']=$record->getFinalPrice();
			$salesUnitInfo['isSelected']=$record->isSelected;
			$data['salesUnits'][]=$salesUnitInfo;
		}
		return $data;
	}
}