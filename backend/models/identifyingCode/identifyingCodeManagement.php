<?php
//验证码管理
namespace backend\models\identifyingCode;
use Yii;
use yii\base\Component;
use yii\base\SmartException;
abstract class identifyingCodeManagement extends Component{
	//验证码订单类型
	const TYPE_MEMBER_SIGN_IN_BY_PHONE=1;
	const TYPE_STAFF_SIGN_IN=2;
	//========================================
	abstract public function handle();
	//========================================
	//验证码订单
	public $order=NULL;
	//========================================
	//创建验证码订单
	public static function creatOrder($type,$data){
		return Yii::$app->smartIdentifyingCode->creatOrder($type,$data);
	}
	//========================================
	//获取验证码订单管理器
	public static function getManagement($orderId,$identifyingCode){
		if(!$orderId) throw new SmartException("miss orderId");
		if(!$identifyingCode) throw new SmartException("miss identifyingCode");
		//校验验证码订单,检验失败内部会抛异常,验证通过拿到验证码订单
		$order=Yii::$app->smartIdentifyingCode->check($orderId,$identifyingCode);
		//返回对应的验证码管理器
		if($order->type==self::TYPE_MEMBER_SIGN_IN_BY_PHONE) 
			return new memberSignInByPhone(array('order'=>$order));
		//找不到合适的管理器
		throw new SmartException("miss identifyingCodeManagement");
	}
}