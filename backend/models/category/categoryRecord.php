<?php
//分类记录
namespace backend\models\category;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
//========================================
class categoryRecord extends SmartActiveRecord{
	//获取分类
	public function getCategory(){return $this->hasOne(category::className(),array('id'=>'categoryId'));}
	//========================================
	//获取资源
	public function getSource(){return source::getRelationShip($this);}
}