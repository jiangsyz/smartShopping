<?php
//sku价格管理器
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\staff\staff;
//========================================
class skuPriceManagement extends Component{
	public $sku=NULL;
	//========================================
	//设置价格
	public function updatePrice($level,$price,staff $staff){
		//获取原价
		$where="`skuId`='{$this->sku->id}' AND `lv`='{$level}'";
		$originaPrice=skuMemberPrice::find()->where($where)->one();
		if(!$originaPrice) throw new SmartException("miss originaPrice");
		//日志
		$log=array();
		$log['handlerType']=$staff->getSourceType();
		$log['handlerId']=$staff->getSourceId();
		$log['skuId']=$this->sku->getSourceId();
		$log['lv']=$level;
		$log['originaPrice']=$originaPrice->price;
		$log['price']=$price;
		skuPriceLog::addObj($log);
		//改价
		$originaPrice->updateObj(array('price'=>$price));
	}
}