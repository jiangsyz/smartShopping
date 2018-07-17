<?php
//标记
namespace backend\models\distribute;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
//========================================
class distribute{
	const TYPE_VIRTUAL="虚拟商品";
	const TYPE_REFRIGERATION="冷链配送";
	const TYPE_NORMAL="常规配送";
}