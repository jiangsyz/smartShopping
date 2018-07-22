<?php
//退款
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
use backend\models\model\source;
//========================================
class refund extends LogActiveRecord{
	//状态
	const STATUS_REJECT=-1;//驳回
	const STATUS_TODO=0;//待办
	const STATUS_REFUNDING=1;//打款中
	const STATUS_REFUND_SUCCESS=2;//打款成功
	const STATUS_REFUND_FAIL=3;//打款失败
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initData"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkPrice"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkPrice"));
	}
	//========================================
	//字段规则
	public function rules(){
		return array(
			//字符串
			[['applyMemo','rejectMemo'],'string','max'=>300],
		);
	}
	//========================================
	//初始化数据
	public function initData(){
		$this->applyTime=time();
		$this->rejectHandlerType=NULL;
		$this->rejectHandlerId=NULL;
		$this->rejectTime=NULL;
		$this->rejectMemo=NULL;
		$this->status=self::STATUS_TODO;
	}
	//========================================
	//校验价格
	public function checkPrice(){if($this->price<=0) throw new SmartException("退款金额错误",2);}
	//========================================
	//打款成功
	public function transactionSuccess(){
		//获取订单
		$orderRecord=orderRecord::getLockedOrderById($this->oid);
		if(!$orderRecord) throw new SmartException("miss orderRecord");
		//退款状态必须为打款中
		if($this->status!=self::STATUS_REFUNDING) throw new SmartException("error refund status");
		//切换状态
		$this->updateObj(array('status'=>self::STATUS_REFUND_SUCCESS));
		//当最后一个退款退款成功,订单的退款状态会改变,检查订单池会完成这个工作
		$orderRecord->refundManagement->checkRefunds();
	}
	//========================================
	//打款失败
	public function transactionFail(){
		//退款状态必须为打款中
		if($this->status!=self::STATUS_REFUNDING) throw new SmartException("error refund status");
		//切换状态
		$this->updateObj(array('status'=>self::STATUS_REFUND_FAIL));	
	}
	//========================================
	//加锁获取某一个退款记录
	public static function getRefund($refundId){
		$table=self::tableName();
		$sql="SELECT * FROM {$table} WHERE `id`='{$refundId}' FOR UPDATE";
		return refund::findBySql($sql)->one();
	}
	//========================================
	//加锁获取某个订单的所有退款记录
	public static function getRefundsOfOrder($orderId){
		$table=self::tableName();
		$sql="SELECT * FROM {$table} WHERE `oid`='{$orderId}' FOR UPDATE";
		return refund::findBySql($sql)->all();
	}
}