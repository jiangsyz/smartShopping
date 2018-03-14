<?php
//员工登录
namespace backend\models\identifyingCode;
use Yii;
use yii\base\Component;
use yii\base\SmartException;
use backend\models\staff\staff;
class staffSignInByPhone extends identifyingCodeManagement{
	public function handle(){
		//获取员工
		$staff=staff::find()->where("`phone`='{$this->order->data}'")->one();
		//会员不存在则初始化会员
		if(!$staff) throw new SmartException("miss staff");
		//返回员工令牌
		return $staff->createToken();
	}
}