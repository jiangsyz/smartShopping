<?php
//订单支付管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderPayManagement extends Component{
	//支付超时秒数
	const PAY_TIME_OUT=60*5
	//========================================
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//检测订单支付超时
	public function checkPayTimeOut(){
		//不是主订单不处理
		if($this->orderRecord->parentId) return;
		//不是未支付状态的不处理
		if($this->orderRecord->payStatus!=0) return;
		//计算等待支付时常
		$timeOut=time()-$this->orderRecord->createTime;
		//超时了修改支付状态
		if($timeOut>self::PAY_TIME_OUT) $this->orderRecord->updateObj(array('payStatus'=>-1));
	}
	//========================================
	//申请支付
	public function applyPay($payType,$appType){
		//检测订单支付超时
		$this->checkPayTimeOut();
		//主订单才能支付
		if($this->orderRecord->parentId) throw new SmartException("is not main order");
		//订单支付状态必须为待支付
		if($this->orderRecord->payStatus!=0) throw new SmartException("error payStatus");
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
        $payCommand['attach']="订单支付";
        $payCommand['body']=$this->orderRecord->id;
        $payCommand['out_trade_no']=Yii::$app->controller->runningId;
        $payCommand['total_fee']=$this->orderRecord->pay;
        //返回调用支付所需的数据
        return Yii::$app->smartWechatPay->applyPay($appType,$payCommand);
	}
}