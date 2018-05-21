<?php
//退款
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
//========================================
class refund extends SmartActiveRecord{
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
	//初始化数据
	public function initData(){
		$this->applyTime=time();
		$this->rejectHandlerType=NULL;
		$this->rejectHandlerId=NULL;
		$this->rejectTime=NULL;
		$this->rejectMemo=NULL;
		$this->refundHandlerType=NULL;
		$this->refundHandlerId=NULL;
		$this->refundTime=NULL;
		$this->refundMemo=NULL;
		$this->status=self::STATUS_TODO;
	}
	//========================================
	//校验价格
	public function checkPrice(){if($this->price<=0) throw new SmartException("退款金额错误",2);}
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