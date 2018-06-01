<?php
//虚拟售卖单元
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
use backend\models\model\salesUnit;
use backend\models\member\member;
use backend\models\member\memberLv;
use backend\models\distribute\distribute;
use backend\models\order\orderBuyingRecord;
//========================================
class virtualItem extends salesUnit{
	//返回资源类型
	public function getSourceType(){return source::TYPE_VIRTUAL_ITEM;}
	//========================================
	//获取最终成交价格
	public function getFinalPrice(member $member){return $this->getPrice();}
	//========================================
	//获取库存(无库存限制返回NULL)
	public function getKeepCount(){return NULL;}
	//========================================
	//获取物流配送方式
	public function getDistributeType(){return distribute::TYPE_VIRTUAL;}
	//========================================
	//获取虚拟产品详情
	public function getDetails(){return $this->hasMany(virtualItemDetail::className(),array('vid'=>'id'));}
	//========================================
	//获取物流
	public function getLogistics(){return NULL;}
	//========================================
	//更新库存
	public function updateKeepCount($handlerType,$handlerId,$keepCount,$memo=NULL){return;}
	//========================================
	//购买成功的处理
	public function buyingSuccess(orderBuyingRecord $r){
		if($r->sourceType!=$this->getSourceType()) 
			throw new SmartException("error orderBuyingRecord sourceType");
		if($r->sourceId!=$this->getSourceId()) 
			throw new SmartException("error orderBuyingRecord sourceId");
		//目前的场景虚拟产品的buyingCount只能为1,为了防止被攻击,这里做一个判断
		if($r->buyingCount!=1) 
			throw new SmartException("error orderBuyingRecord buyingCount");
		//根据收益类型执行相应操作
		foreach($this->details as $d){
			if($d->benefitType=="member")
				memberLv::addVip($r,json_decode($d->benefitDetail,true));
			else
				throw new SmartException("error benefitType");
		}		
	}
}