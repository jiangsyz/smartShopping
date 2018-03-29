<?php
//分类
namespace backend\models\category;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class category extends SmartActiveRecord{
	//获取子分类
	public function getChildren(){return $this->hasMany(self::className(),array('pid'=>'id'));}
	//========================================
	//获取后代分类
	public function getPosterities(){
		//用栈代替递归,寻找直接子分类和间接子分类
		$checkList=$posterities=$this->children;
		while(!empty($checkList)){
			$check=array_shift($checkList);
			foreach($check->children as $c) $checkList[]=$posterities[]=$c;
		}
		return $posterities;
	}
	//========================================
	//获取所有的顶级分类
	static public function getTopCategories(){
		return self::find()->where("`pid` is NULL")->orderBy("`sort` ASC")->all();
	}

}