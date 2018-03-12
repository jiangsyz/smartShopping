<?php
//会员通过
namespace backend\models\identifyingCode;
use Yii;
use yii\base\Component;
use yii\base\SmartException;
use backend\models\member\member;
class memberSignInByPhone extends identifyingCodeManagement{
	public function handle(){
		//校验注册数据
		$data=json_decode($this->order->data,true);
		if(!isset($data['phone'])) throw new SmartException("order data miss phone");
		//获取会员
		$member=member::find()->where("`phone`='{$data['phone']}'")->one();
		//会员不存在则初始化会员
		if(!$member) $member=member::addObj(array('phone'=>$data['phone']));
		//返回会员令牌
		return $member->createToken();
	}
}