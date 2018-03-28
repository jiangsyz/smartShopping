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
	//========================================
	//获取地址
	public function getAddress(){
		//订单
		$oRecord=$this->orderRecord;
		//获取地址属性
		$addressList=$oRecord->propertyManagement->getProperty('address');
		//当前订单存在地址
		if($addressList){
			//每个订单只能有一个地址
			if(count($addressList)!=1) 
				throw new SmartException("order {$oRecord->id} error address count");
			//返回地址
			return $addressList[0];
		}
		//如果是主订单找不到地址,返回NULL
		if(!$oRecord->parentId) return NULL;
		//如果是子订单找不到地址,获取父订单地址
		$parent=$oRecord->parentOrder;
		if(!$parent) 
			throw new SmartException("order {$oRecord->id} miss parent");
		return $parent->addressManagement->getAddress();
	}
}