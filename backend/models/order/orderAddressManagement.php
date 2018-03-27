<?php
//订单地址管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\member\address;
//========================================
class orderAddressManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//添加收货地址
	public function addAddress(){
		//订单
		$oRecord=$this->orderRecord;
		//不需要收货地址的情况
		if(!$oRecord->isNeedAddress) return;
		//确定命令中表明该订单记录收货地址的索引
		$addressIndex='address_'.$oRecord->index;
		//客户端没有提交地址
		if(!isset($oRecord->command[$addressIndex])) return;
		//获取地址
		$where="`id`='{$oRecord->command[$addressIndex]}' ";
		$where.="AND `memberId`='{$oRecord->memberId}' AND `isDeled`='0'";
		$address=address::find()->where($where)->one();
		if(!$address) throw new SmartException("miss address");
		//添加收货地址
		$oRecord->propertyManagement->addProperty('address',json_encode($address->getData()));
	}
}