<?php
//登录行为管理器
namespace backend\models\member;
use Yii;
use yii\base\Component;
use yii\base\SmartException;
use backend\models\identifyingCode\identifyingCodeManagement;
//========================================
class signInManagement extends Component{
	//通过手机号申请登陆
	public static function applySignInByPhone($phone){
		if(!$phone) throw new SmartException("miss phone");
		//创建验证码订单
		$type=identifyingCodeManagement::TYPE_MEMBER_SIGN_IN_BY_PHONE;
		$data=json_encode(array('phone'=>$phone));
		$order=identifyingCodeManagement::creatOrder($type,$data);
		//发送短信
		Yii::$app->smartSms->send($phone,"登录验证码:{$order->identifyingCode}");
		//返回验证码订单编号
		return $order->id;
	}
}