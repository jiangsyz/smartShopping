<?php
//登录行为管理器
namespace backend\models\signInManagement;
use Yii;
use yii\base\Component;
use yii\base\SmartException;
use backend\models\identifyingCode\identifyingCodeManagement;
use backend\models\member\member;
use backend\models\staff\staff;
//========================================
class signInManagement extends Component{
	//会员通过手机号申请登陆
	public static function memberApplySignInByPhone($phone){
		//创建验证码订单
		$type=identifyingCodeManagement::TYPE_MEMBER_SIGN_IN_BY_PHONE;
		$order=identifyingCodeManagement::creatOrder($type,$phone);
		//发送短信
		Yii::$app->smartSms->send($phone,"会员登录验证码:{$order->identifyingCode}");
		//返回验证码订单编号
		return $order->id;
	}
	//========================================
	//员工通过手机号申请登陆
	public static function staffApplySignInByPhone($phone){
		//查找会员
		$staff=staff::find()->where("`phone`='{$phone}'")->one();
		if(!$staff) throw new SmartException("miss staff");
		//创建验证码订单
		$type=identifyingCodeManagement::TYPE_STAFF_SIGN_IN_BY_PHONE;
		$order=identifyingCodeManagement::creatOrder($type,$phone);
		//发送短信
		Yii::$app->smartSms->send($phone,"员工登录验证码:{$order->identifyingCode}");
		//返回验证码订单编号
		return $order->id;
	}
}