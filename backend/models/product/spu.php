<?php
//标准售卖单元
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
use backend\models\model\product;
use backend\models\member\member;
use backend\models\distribute\distribute;
//========================================
class spu extends product{
	//返回资源类型
	public function getSourceType(){return source::TYPE_SPU;}
	//========================================
	//获取物流配送方式
	public function getDistributeType(){
		if($this->distributeType==1) return distribute::TYPE_REFRIGERATION;
		if($this->distributeType==2) return distribute::TYPE_NORMAL;
		throw new SmartException("error distributeType");
	}
	//========================================
	//获取sku
	public function getSkus(){return $this->hasMany(sku::className(),array('spuId'=>'id'));}
	//========================================
	//获取销售价最便宜的sku
	public function getCheapestSku(){
		$cheapestSku=NULL;
		//统计销售价最便宜的
		foreach($this->skus as $sku){
			if(!$cheapestSku) $cheapestSku=$sku;
			if($sku->getPrice()<$cheapestSku->getPrice()) $cheapestSku=$sku;
		}
		//没有找到最便宜的报错
		if(!$cheapestSku) throw new SmartException("miss cheapestSku");
		//返回最便宜的
		return $cheapestSku;
	}
	//========================================
	//获取数据提取器
	public function getExtraction(){return new spuExtraction($this);}
}