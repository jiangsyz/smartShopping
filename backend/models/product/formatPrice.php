<?php
//spu数据提取器
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
//========================================
class formatPrice{
	//格式化价格
	static public function formatPrice($price){return number_format(floatval($price),2);}
}