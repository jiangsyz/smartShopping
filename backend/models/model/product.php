<?php
//产品
namespace backend\models\model;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\member\member;
//========================================
abstract class product extends source{
	//判断是否下架(true=下架了)
	public function isClosed(){if($this->closed==1) return true; else return false;}
	//========================================
	//是否允许销售(true=允许销售)
	public function isAllowSale(){
		if($this->isLocked()) return false;
		if($this->isClosed()) return false;
		return true;
	}
	//========================================
	//获取商家
	public function getShop(){return $this->hasOne(member::className(),array('id'=>'memberId'));}
	//========================================
	//获取产品类型
	public function getProductType(){return $this->getSourceType();}
	//========================================
	//获取产品id
	public function getProductId(){return $this->getSourceId();}
	//========================================
	//获取产品名称
	public function getProductName(){return $this->title;}
	//========================================
	//获取产品描述
	public function getProductDesc(){return $this->desc;}
	//========================================
	//获取封面
	public function getCover(){return $this->cover;}
	//========================================
	//获取物流配送方式
	abstract public function getDistributeType();
}