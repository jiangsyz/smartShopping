<?php
//退款回调
namespace wechatRefund\models;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class refundCallback extends SmartActiveRecord{
	//指定库
	public static function getDb(){return Yii::$app->log_db;}
}