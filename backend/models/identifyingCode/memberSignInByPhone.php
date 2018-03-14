<?php
//会员登录
namespace backend\models\identifyingCode;
use Yii;
use yii\base\Component;
use yii\base\SmartException;
use backend\models\member\member;
class memberSignInByPhone extends identifyingCodeManagement{
	public function handle(){
		//获取会员
		$member=member::find()->where("`phone`='{$this->order->data}'")->one();
		//会员不存在则初始化会员
		if(!$member) $member=member::addObj(array('phone'=>$this->order->data));
		//返回会员令牌
		return $member->createToken();
	}
}