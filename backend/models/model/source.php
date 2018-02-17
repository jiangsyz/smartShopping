<?php
//资源
namespace backend\models\model;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use yii\db\ActiveRecord;
use backend\models\mark\mark;
use backend\models\member\member;
use backend\models\product\spu;
//========================================
abstract class source extends SmartActiveRecord{
	//资源类型
	const TYPE_SPU=1;
	const TYPE_SKU=2;
	const TYPE_MEMBER=3;
	const TYPE_STAFF=4;
	const TYPE_ARTICLE=5;
	const TYPE_ORDER_RECORD=6;
	const TYPE_VIRTUAL_ITEM=7;
	//========================================
	//返回资源类型
	abstract public function getSourceType();
	//========================================
	//返回资源id
	public function getSourceId(){return $this->id;}
	//========================================
	//返回资源全局编号
	public function getSourceNo(){return $this->getSourceType().'_'.$this->getSourceId();}
	//========================================
	//判断资源是否被锁定
	public function isLocked(){if($this->locked==0) return false; else return true;}
	//========================================
	//以sourceType和sourceId字段作为外键来获取资源
	static public function getSource(ActiveRecord $ar){
		//spu
		if($ar->sourceType==self::TYPE_SPU){
			return $ar->hasOne(spu::className(),array('id'=>'sourceId')); 
		}
		//会员
		if($ar->sourceType==self::TYPE_MEMBER){
			return $ar->hasOne(member::className(),array('id'=>'sourceId')); 
		}
		//错误的资源类型
		throw new SmartException("error source type");
	}
}