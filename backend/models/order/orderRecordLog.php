<?php
//订单记录
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
//========================================
class orderRecordLog extends LogActiveRecord{
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initTime"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initRunningId"));
	}
	//========================================
	public function initTime(){$this->time=time();}
	//========================================
	public function initRunningId(){
		if(!Yii::$app->controller->runningId)throw new SmartException("miss runningId");
		$this->runningId=Yii::$app->controller->runningId;
	}
}