<?php
//订单地址管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\member\address;
use backend\models\order\orderStatusManagement;
use backend\models\staff\staff;
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
	//修改收货地址
	public function changeAddress($addressId){
		//订单
		$oRecord=$this->orderRecord;
		//不需要收货地址的情况
		if(!$oRecord->isNeedAddress) throw new SmartException("is not need address");
		//获取订单状态
		$status=$oRecord->statusManagement->getStatus();
		if($status!=orderStatusManagement::STATUS_UNPAID) throw new SmartException("error status");
		//获取地址
		$where="`id`='{$addressId}' ";
		$where.="AND `memberId`='{$oRecord->memberId}' AND `isDeled`='0'";
		$address=address::find()->where($where)->one();
		if(!$address) throw new SmartException("miss address");
		//获取原有的地址
		$oldAddress=$oRecord->propertyManagement->getProperty("address");
		//删除原有地址
		foreach($oldAddress as $old) $oRecord->propertyManagement->delProperty($old['obj']);
		//添加收货地址
		$oRecord->propertyManagement->addProperty('address',json_encode($address->getData()));
	}
	//========================================
	//员工修改地址
	public function changeAddressByStaff(staff $staff,$name,$phone,$areaId,$address,$postCode=NULL){
		//校验参数
		if(!$name) throw new SmartException("收件人姓名缺失",-2);
		if(!$phone) throw new SmartException("收件人电话缺失",-2);
		if(!$areaId) throw new SmartException("收件人区域缺失",-2);
		if(!$address) throw new SmartException("收件人地址缺失",-2);
		//订单
		$oRecord=$this->orderRecord;
		//不需要收货地址的情况
		if(!$oRecord->isNeedAddress) throw new SmartException("is not need address");
		//获取订单状态(只有待发货状态,客服才能修改地址)
		$status=$oRecord->statusManagement->getStatus();
		if($status!=orderStatusManagement::STATUS_UNDELIVERED) throw new SmartException("error status");
		//获取地域
		$area=Yii::$app->smartArea->getArea($areaId); if(!$area) throw new SmartException("找不到收货区域",-2);
		//区域等级校验
		if($area->level!=3) throw new SmartException("错误的收货区域等级",-2);
		//构建地址
		$addressData=array();
		$addressData['id']=NULL;
		$addressData['memberId']=NULL;
		$addressData['name']=$name;
		$addressData['phone']=$phone;
		$addressData['areaId']=$areaId;
		$addressData['address']=$address;
		$addressData['postCode']=$postCode?$postCode:NULL;
		$addressData['createTime']=time();
		$addressData['isDeled']=NULL;
		$addressData['fullAreaName']=$area->full_area_name;
		$addressData['staffId']=$staff->id;
		//将新地址加入
		$oRecord->propertyManagement->addProperty('address',json_encode($addressData));
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
			//取最后修改的
			$property=NULL;
			foreach($addressList as $address){
				if(!$property || $address['time']>$property['time']) $property=$address;
			}
			//提取地址
			return json_decode($property['val'],true);
		}
		//如果是主订单找不到地址,返回NULL
		if(!$oRecord->parentId) return NULL;
		//如果是子订单找不到地址,获取父订单地址
		$parent=$oRecord->relationManagement->getParent();
		if(!$parent) throw new SmartException("order {$oRecord->id} miss parent");
		return $parent->addressManagement->getAddress();
	}
}