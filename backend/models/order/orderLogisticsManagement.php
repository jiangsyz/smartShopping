<?php
//订单物流管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderLogisticsManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//获取物流列表
	public function getLogisticsList(){
		$logisticsList=array();
		//获取所有购买行为
		$buyingRecords=$this->orderRecord->buyingManagement->getBuyingList();
		//逐个提取每个购物行为的物流信息
		foreach($buyingRecords as $b){
			//没有回填单号的跳过
			if(!$b->logisticsCode) continue;
			//获取物流渠道
			$logistics=$b->salesUnit->getLogistics();
			if(!$logistics) throw new SmartException("buyingRecord {$b->id} miss logistics");
			//获取物流列表索引(物流渠道id_物流号)
			$index=trim($logistics->id).'_'.trim($b->logisticsCode);
			//初始化对应索引的物流列表
			if(!isset($logisticsList[$index])) 
				$logisticsList[$index]=array('logisticsCode'=>$b->logisticsCode,'logistics'=>$logistics);
			//将购物行为加入购物列表
			$logisticsList[$index]['buyingRecords'][]=$b;
		}
		return $logisticsList;
	}
}