<?php
//虚拟售卖单元
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
use backend\models\model\salesUnit;
use backend\models\member\member;
//========================================
class virtualItem extends salesUnit{
	//返回资源类型
	public function getSourceType(){return source::TYPE_VIRTUAL_ITEM;}
	//========================================
	//是否需要收货地址
	public function isNeedAddress(){return false;}
	//========================================
	//获取最终成交价格
	public function getFinalPrice(member $member){return $this->getPrice();}
	//========================================
	//获取库存(无库存限制返回NULL)
	public function getKeepCount(){return NULL;}
	//========================================
	//更新库存
	public function updateKeepCount($count){throw new SmartException("virtualItem keepCount is null");}
}