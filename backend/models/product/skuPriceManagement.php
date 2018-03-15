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
		//获取某个等级的价格
		$p=skuMemberPrice::find()->where("`skuId`='{$this->sku->id}' AND `lv`='{$level}'")->one();
		if(!$p) throw new SmartException("miss level price");
		//改价
		$p->updateObj(array('price'=>$price));
	}
}