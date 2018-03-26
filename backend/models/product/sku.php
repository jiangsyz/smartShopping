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
	//获取针对某个会员等级的售卖价格
	public function getLevelPrice($level){
		//获取某个等级的价格
		$price=skuMemberPrice::find()->where("`skuId`='{$this->id}' AND `lv`='{$level}'")->one();
		//找到当前等级价格直接返回
		if($price) return $price->price;
		//原价找不到直接报错,其他等级找不到返回原价
		if($level==0) throw new SmartException("miss lv0 price"); else return $this->getPrice();
	}
	//========================================
	//获取售卖价格(原价)
	public function getPrice(){return $this->getLevelPrice(0);}
	//========================================
	//获取最终成交价格
	public function getFinalPrice(member $member){return $this->getLevelPrice($member->getLevel());}
	//========================================
	//获取库存(无库存限制返回NULL)
	public function getKeepCount(){return $this->count;}
	//========================================
	//获取物流配送方式
	public function getDistributeType(){return $this->spu->getDistributeType();}
	//========================================
	//更新库存
	public function updateKeepCount($handlerType,$handlerId,$keepCount,$memo=NULL){
		//sku库存管理器,创建同时也就完成了库存修改
		$data=array();
		$data['sku']=$this;
		$data['handlerType']=$handlerType;//操作者类型
		$data['handlerId']=$handlerId;//操作者数据
		$data['keepCount']=$keepCount;//修改后的库存
		$data['memo']=$memo;//备注
		new skuKeepCountManagement($data);
	}
	//========================================
	//获取spu
	public function getSpu(){return $this->hasOne(spu::className(),array('id'=>'spuId'));}
}