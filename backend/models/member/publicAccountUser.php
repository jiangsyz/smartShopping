<?php
//公众号用户
namespace backend\models\member;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class publicAccountUser extends SmartActiveRecord{
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initTime"));
	}
	//========================================
	public function initTime(){$this->time=time();}
}