<?php
//分类记录
namespace backend\models\category;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
use backend\models\model\source;
//========================================
class categoryRecord extends LogActiveRecord{
	//获取分类
	public function getCategory(){return $this->hasOne(category::className(),array('id'=>'categoryId'));}
	//========================================
	//获取资源
	public function getSource(){return source::getRelationShip($this);}
}