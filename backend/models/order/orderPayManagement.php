<?php
//订单支付管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderPayManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//申请支付
	public function applyPay($payType,$appType){
		if($payType=='wechat') return $this->applyWechatPay($appType);
		throw new SmartException("error payType");
	}
	//========================================
	//申请微信支付
	public function applyWechatPay($appType){
		//创建支付信息
		$payCommand=array();
        $payCommand['attach']="订单支付";
        $payCommand['body']=$this->orderRecord->id;
        $payCommand['out_trade_no']=Yii::$app->controller->runningId;
        $payCommand['total_fee']=$this->orderRecord->pay;
        //返回调用支付所需的数据
        return Yii::$app->smartWechatPay->applyPay($appType,$payCommand);
	}
}