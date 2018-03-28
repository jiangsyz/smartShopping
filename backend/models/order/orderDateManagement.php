<?php
//订单预期收货日管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderDateManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//添加期望收货日期
	public function addDate(){
		//订单
		$oRecord=$this->orderRecord;
		//确定命令中表明该订单期望收货日期的索引
		$dateIndex='date_'.$oRecord->index;
		//客服端没有为该订单指定预期收获日期
		if(!isset($oRecord->command[$dateIndex])) return;
		//添加期望收货日期
		$oRecord->propertyManagement->addProperty('date',$oRecord->command[$dateIndex]);
	}
	//========================================
	//获取预期收货日期
	public function getDate(){
		//订单
		$oRecord=$this->orderRecord;
		//获取预期收货日期属性
		$dateList=$oRecord->propertyManagement->getProperty('date');
		//当前订单有预期收货日期
		if($dateList){
			//每个订单只能有一个预期收货日期
			if(count($dateList)!=1)
				throw new SmartException("order {$oRecord->id} error date count");
			//返回预期收货日期
			return $dateList[0];
		}
		//如果是主订单找不到预期收货日期,返回NULL
		if(!$oRecord->parentId) return NULL;
		//如果是子订单找不到预期收货日期,获取父订单预期收货日期
		$parent=$oRecord->parentOrder;
		if(!$parent) throw new SmartException("order {$oRecord->id} miss parent");
		return $parent->dateManagement->getDate();
	}
}