<?php
//售卖单元
namespace backend\models\model;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\member\member;
//========================================
abstract class salesUnit extends product{
	//获取售卖单元名称
	public function getSalesUnitName(){return $this->title;}
	//========================================
	//获取售卖价格(原价)
	public function getPrice(){return $this->price;}
	//========================================
	//获取针对某个会员等级的售卖价格
	public function getLevelPrice($level){return $this->getPrice();}
	//========================================
	//获取会员最终成交价格
	abstract public function getFinalPrice(member $member);
	//========================================
	//获取库存(无库存限制返回NULL)
	abstract public function getKeepCount();
	//========================================
	//更新库存
	abstract public function updateKeepCount($count);
	//========================================
	//是否需要收货地址
	abstract public function isNeedAddress();
}