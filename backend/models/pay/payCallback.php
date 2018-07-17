<?php
//支付回调
namespace backend\models\pay;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
//========================================
class payCallback extends LogActiveRecord{
	//指定库
	public static function getDb(){return Yii::$app->log_db;}
}