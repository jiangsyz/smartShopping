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
		//备注为空不处理
		if(!$oRecord->command[$memoIndex]) return;
		//添加会员备注
		$oRecord->propertyManagement->addProperty('memberMemo',$oRecord->command[$memoIndex]);
		//将订单状态改为锁定
		$oRecord->updateObj(array('locked'=>1));
	}
	//========================================
	//获取备注
	public function getMemo($type){
		//订单
		$oRecord=$this->orderRecord;
		//根据type确定取会员备注或员工备注
		$key=false;
		if($type==1) $key='memberMemo';
		if($type==2) $key='staffMemo';
		if(!$key) throw new SmartException("error memo type");
		//获取备注属性
		$memoList=$oRecord->propertyManagement->getProperty($key);
		//当前订单有备注,直接返回
		if($memoList) return $memoList;
		//如果是主订单找不到备注,返回NULL
		if(!$oRecord->parentId) return NULL;
		//如果是子订单找不到备注,获取父订单备注
		$parent=$oRecord->parentOrder;
		if(!$parent) throw new SmartException("order {$oRecord->id} miss parent");
		return $parent->memoManagement->getMemo($type);
	}
}