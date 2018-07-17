<?php
//退款回调
namespace wechatRefund\models;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
//========================================
class refundCallback extends LogActiveRecord{
	//指定库
	public static function getDb(){return Yii::$app->log_db;}
}