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
}