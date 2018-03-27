<?php
//订单备注管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderMemoManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//添加会员备注
	public function addMemberMemo(){
		//订单
		$oRecord=$this->orderRecord;
		//确定命令中表明该订单备注的索引
		$memoIndex='memo_'.$oRecord->index;
		//客服端没有为该订单指定备注
		if(!isset($oRecord->command[$memoIndex])) return;
		//添加会员备注
		$oRecord->propertyManagement->addProperty('memberMemo',$oRecord->command[$memoIndex]);
	}
}