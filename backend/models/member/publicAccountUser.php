<?php
//公众号用户
namespace backend\models\member;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
//========================================
class publicAccountUser extends LogActiveRecord{
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initTime"));
	}
	//========================================
	public function initTime(){$this->time=time();}
}