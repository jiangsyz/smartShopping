<?php
//标记
namespace backend\models\distribute;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class distribute{
	const TYPE_VIRTUAL="无需配送";
	const TYPE_REFRIGERATION="冷链配送";
	const TYPE_NORMAL="常规配送";
}