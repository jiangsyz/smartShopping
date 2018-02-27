<?php
//分类
namespace backend\models\category;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class category extends SmartActiveRecord{
	//获取所有的顶级分类
	static public function getTopCategories(){
		return self::find()->where("`pid` is NULL")->orderBy("`sort` ASC")->all();
	}
}