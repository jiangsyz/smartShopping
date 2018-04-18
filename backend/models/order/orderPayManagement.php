<?php
//订单支付管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\model\source;
//========================================
class orderPayManagement extends Component{
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
	//========================================
	//获取支付剩余时间
	public function getPayRemainingTime(){
		//支付过期时间戳
		$timeOut=$this->orderRecord->createTime+Yii::$app->params["payTimeOut"];
		//剩余支付时间
		$remainingTime=$timeOut-time();
		//剩余时间不能是负数
		return $remainingTime>0?$remainingTime:0;
	}
	//========================================
	//修改价格
	public function changePrice(source $handler,$price,$memo){
		//校验价格
		if($price<=0) throw new SmartException("订单商品价格不能等于0",-2);
		//判断是否有修改
		if($price==$this->orderRecord->finalPrice) throw new SmartException("订单商品价格未修改",-2);
		//判断一笔订单能否被支付
		if(!$this->canOrderPay()) throw new SmartException("订单不能被支付",-2);
		//快照
		$dataPhoto=$this->orderRecord->getData();
		//修改价格
		$data=array();
		$data['finalPrice']=$price;
		$data['pay']=($price+$this->orderRecord->freight)*100;
		$data['reduction']=0;
		$this->orderRecord->updateObj($data);
		//记录日志
		$this->logChange($handler,$memo,$dataPhoto);
	}
	//========================================
	//修改运费
	public function changeFreight(source $handler,$freight,$memo){
		//校验运费
		if($freight<0) throw new SmartException("订单运费不能小于0",-2);
		//判断是否有修改
		if($freight==$this->orderRecord->freight) throw new SmartException("订单运费未修改",-2);
		//判断一笔订单能否被支付
		if(!$this->canOrderPay()) throw new SmartException("订单不能被支付",-2);
		//快照
		$dataPhoto=$this->orderRecord->getData();
		//修改价格
		$data=array();
		$data['freight']=$freight;
		$data['pay']=($freight+$this->orderRecord->finalPrice)*100;
		$this->orderRecord->updateObj($data);
		//记录日志
		$this->logChange($handler,$memo,$dataPhoto);
	}
	//========================================
	//修改价格或运费的日志
	private function logChange(source $handler,$memo,$dataPhoto){
		//校验快照数据
		if(!isset($dataPhoto['finalPrice'])) throw new SmartException("dataPhoto miss finalPrice");
		if(!isset($dataPhoto['freight'])) throw new SmartException("dataPhoto miss freight");
		if(!isset($dataPhoto['reduction'])) throw new SmartException("dataPhoto miss reduction");
		if(!isset($dataPhoto['pay'])) throw new SmartException("dataPhoto miss pay");
		//老数据
		$originaData=array();
		$originaData['finalPrice']=$dataPhoto['finalPrice'];
		$originaData['freight']=$dataPhoto['freight'];
		$originaData['reduction']=$dataPhoto['reduction'];
		$originaData['pay']=$dataPhoto['pay'];
		//新数据
		$data=array();
		$data['finalPrice']=$this->orderRecord->finalPrice;
		$data['freight']=$this->orderRecord->freight;
		$data['reduction']=$this->orderRecord->reduction;
		$data['pay']=$this->orderRecord->pay;
		//记录日志
		$log=array();
		$log['handlerType']=$handler->getSourceType();
		$log['handlerId']=$handler->getSourceId();
		$log['orderId']=$this->orderRecord->id;
		$log['originaData']=json_encode($originaData);
		$log['data']=json_encode($data);
		$log['memo']=$memo;
		changeOrderPriceLog::addObj($log);
	}
}