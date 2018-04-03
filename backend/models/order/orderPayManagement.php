<?php
//订单支付管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderPayManagement extends Component{
	//支付超时秒数
	const PAY_TIME_OUT=60*5;
	//========================================
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//判断一笔订单能否被支付
	public function canOrderPay(){
		//获取订单状态
		$status=$this->orderRecord->statusManagement->getStatus();
		//待支付状态的订单允许支付,其他状态不允许
		if($status==orderStatusManagement::STATUS_UNPAID) 
			return true;
		else
			return false;
	}
	//========================================
	//申请支付
	public function applyPay($payType,$appType){
		//判断一笔订单能否被支付
		if(!$this->canOrderPay()) throw new SmartException("can't pay");
		//根据不同的支付渠道申请支付
		if($payType=='wechat') return $this->applyWechatPay($appType);
		//错误的支付渠道
		throw new SmartException("error payType");
	}
	//========================================
	//申请微信支付
	public function applyWechatPay($appType){
		//创建支付信息
		$payCommand=array();
        $payCommand['attach']=$this->orderRecord->id;
        $payCommand['body']="订单支付";
        $payCommand['out_trade_no']=Yii::$app->controller->runningId;
        $payCommand['total_fee']=$this->orderRecord->pay;
        //返回调用支付所需的数据
        return Yii::$app->smartWechatPay->applyPay($appType,$payCommand);
	}
	//========================================
	//支付成功
	public function paySuccess($runningId){
		//只有待支付状态的订单可以支付成功
		if(!$this->canOrderPay()) throw new SmartException("can't pay");
		//修改支付状态
		$this->orderRecord->updateObj(array('payStatus'=>1));
		//记录支付回调的runningId
		$this->orderRecord->propertyManagement->addProperty("payRunningId",$runningId);
	}
}