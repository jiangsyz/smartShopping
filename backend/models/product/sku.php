<?php
//库存计量单元
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
use backend\models\model\salesUnit;
use backend\models\member\member;
//========================================
class sku extends salesUnit{
	//返回资源类型
	public function getSourceType(){return source::TYPE_SKU;}
	//========================================
	//判断是否业务锁定
	public function isLocked(){
		if($this->locked) return true;
		if($this->spu->isLocked()) return true;
		return false;
	}
	//========================================
	//判断是否下架(true=下架了)
	public function isClosed(){
		if($this->closed) return true;
		if($this->spu->isClosed()) return true;
		return false;
	}
	//========================================
	//获取商家
	public function getShop(){return $this->spu->shop;}
	//========================================
	//获取产品类型
	public function getProductType(){return $this->spu->getSourceType();}
	//========================================
	//获取产品id
	public function getProductId(){return $this->spu->getSourceId();}
	//========================================
	//获取产品名称
	public function getProductName(){return $this->spu->title;}
	//========================================
	//获取封面
	public function getCover(){return $this->spu->cover;}
	//========================================
	//获取最终成交价格
	public function getFinalPrice(member $member){return $this->getPrice();}
	//========================================
	//获取库存(无库存限制返回NULL)
	public function getKeepCount(){return $this->count;}
	//========================================
	//更新库存($count=修改量)
	public function updateKeepCount($count){
		//被锁定不能修改库存
		if($this->isLocked()) throw new SmartException("salesUnit locked");
		//更新量不能为0
		if($count==0) throw new SmartException("count==0");
		//修改库存
		$this->updateObj(array('count'=>$this->count+$count));
	}
	//========================================
	//获取spu
	public function getSpu(){return $this->hasOne(spu::className(),array('id'=>'spuId'));}
}