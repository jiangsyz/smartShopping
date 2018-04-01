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
	//判断一笔订单能否被支付
	public function canOrderPay(){
		//不是主订单不能被处理
		if($this->orderRecord->parentId) return false;
		//已经被取消的不能被支付
		if($this->orderRecord->cancelStatus==1) return false;
		//支付状态不是未支付的不能被支付
		if($this->orderRecord->payStatus!=0) return false;
		//计算等待支付时常
		$waiting=time()-$this->orderRecord->createTime;
		//超时了修改支付状态,不能被支付
		if($waiting>self::PAY_TIME_OUT){
			$this->orderRecord->updateObj(array('payStatus'=>-1));
			return false;
		}
		//允许支付
		return true;
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
        $payCommand['attach']="订单支付";
        $payCommand['body']=$this->orderRecord->id;
        $payCommand['out_trade_no']=Yii::$app->controller->runningId;
        $payCommand['total_fee']=$this->orderRecord->pay;
        //返回调用支付所需的数据
        return Yii::$app->smartWechatPay->applyPay($appType,$payCommand);
	}
}