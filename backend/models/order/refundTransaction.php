<?php
//退款交易
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class refundTransaction extends SmartActiveRecord{
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initTransactionHandlerTime"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initStatus"));
	}
	//========================================
	//初始化交易时间
	public function initTransactionHandlerTime(){$this->transactionHandlerTime=time();}
	//========================================
	//初始化交易状态
	public function initStatus(){$this->status=0;}
	//========================================
	//获取退款
	public function getRefund(){
		$table=refund::tableName();
		$sql="SELECT * FROM {$table} WHERE `id`='{$this->refundId}' FOR UPDATE;";
		return refund::findBySql($sql)->one();
	}
	//========================================
	//交易成功
	public function transactionSuccess(){
		//交易状态必须是打款中才能切换至成功
		if($this->status!=0) throw new SmartException("error transaction status");
		//获取退款
		$refund=$this->getRefund(); if(!$refund) throw new SmartException("miss refund");
		//将交易状态切换为成功
		$this->updateObj(array('status'=>1));
		//将退款状态切为打款成功
		$refund->transactionSuccess();
	}
	//========================================
	//交易失败
	public function transactionFail(){
		//交易状态必须是打款中才能切换至失败
		if($this->status!=0) throw new SmartException("error transaction status");
		//获取退款
		$refund=$this->getRefund(); if(!$refund) throw new SmartException("miss refund");
		//将交易状态切换为成功
		$this->updateObj(array('status'=>-1));
		//将退款状态切为打款失败
		$refund->transactionFail();
	}
}