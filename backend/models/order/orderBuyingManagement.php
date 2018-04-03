<?php
//订单购买行为管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderBuyingManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//获取直接的购买行为
	public function getBuyingRecords($lockFlag=false){
		if($lockFlag){
			$table=orderBuyingRecord::tableName();
			$sql="SELECT * FROM {$table} WHERE `orderId`='{$this->orderRecord->id}' FOR UPDATE";
			return orderBuyingRecord::findBySql($sql)->all();
		}
		else{
			return orderBuyingRecord::find()->where("`orderId`='{$this->orderRecord->id}'")->all();
		}
	}
	//========================================
	//获取购买行为列表
	public function getBuyingList($lockFlag=false){
		//获取直接购买行为
		$buyingList=$this->getBuyingRecords($lockFlag);
		//获取后代订单
		$posterities=$this->orderRecord->relationManagement->getPosterities();
		//获取后代购买行为
		foreach($posterities as $p){
			$buyingRecords=$p->buyingManagement->getBuyingRecords($lockFlag);
			foreach($buyingRecords as $buyingRecord) $buyingList[]=$buyingRecord;
		}
		return $buyingList;

	}
}