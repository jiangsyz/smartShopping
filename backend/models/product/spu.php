<?php
//标准售卖单元
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
use backend\models\model\product;
use backend\models\member\member;
//========================================
class spu extends product{
	//返回资源类型
	public function getSourceType(){return source::TYPE_SPU;}
	//========================================
	//获取sku
	public function getSkus(){return $this->hasMany(sku::className(),array('spuId'=>'id'));}
}