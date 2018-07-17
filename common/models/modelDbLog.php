<?php
//数据日志
namespace common\models;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class modelDbLog extends SmartActiveRecord{
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
		if(!Yii::$app->controller->runningId) throw new SmartException("miss runningId");
		$this->runningId=Yii::$app->controller->runningId;
	}
}