<?php
//支付回调
namespace backend\models\pay;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class payCallback extends SmartActiveRecord{
	//指定库
	public static function getDb(){return Yii::$app->log_db;}
}