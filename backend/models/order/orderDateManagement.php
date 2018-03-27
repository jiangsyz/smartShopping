<?php
//订单预期送货日管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderDateManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//添加期望收获日期
	public function addDate(){
		//订单
		$oRecord=$this->orderRecord;
		//确定命令中表明该订单期望收获日期的索引
		$dateIndex='date_'.$oRecord->index;
		//客服端没有为该订单指定备注
		if(!isset($oRecord->command[$dateIndex])) return;
		//添加期望收货日期
		$oRecord->propertyManagement->addProperty('date',$oRecord->command[$dateIndex]);
	}
}