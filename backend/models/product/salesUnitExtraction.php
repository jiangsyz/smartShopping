<?php
//salesUnit数据提取器
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use backend\models\member\member;
use backend\models\model\salesUnit;
//========================================
class salesUnitExtraction{
	private $salesUnit=false;
	//========================================
	public function __construct(salesUnit $salesUnit){$this->salesUnit=$salesUnit;}
	//========================================
	//获取基础数据
	public function getBasicData(member $member){
		$shoppingCartRecord=$this->salesUnit->getShoppingCartRecord($member);
		$data=array();
		$data['sourceType']=$this->salesUnit->getSourceType();
		$data['sourceId']=$this->salesUnit->getSourceId();
		$data['productType']=$this->salesUnit->getProductType();
		$data['productId']=$this->salesUnit->getProductId();
		$data['title']=$this->salesUnit->getSalesUnitName();
		$data['price']=formatPrice::formatPrice($this->salesUnit->getLevelPrice(0));
		$data['memberPrice']=formatPrice::formatPrice($this->salesUnit->getLevelPrice(1));
		$data['reduction']=formatPrice::formatPrice($this->salesUnit->getReduction());
		$data['keepCout']=$this->salesUnit->getKeepCount();
		$data['shoppingCartCount']=$shoppingCartRecord?$shoppingCartRecord->count:0;
		$data['isAllowSale']=$this->salesUnit->isAllowSale();
		return $data;
	}
}