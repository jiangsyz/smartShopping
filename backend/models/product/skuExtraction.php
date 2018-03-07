<?php
//sku数据提取器
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use backend\models\member\member;
//========================================
class skuExtraction{
	private $sku;
	//========================================
	public function __construct(sku $sku){$this->sku=$sku;}
	//========================================
	//获取基础数据
	public function getBasicData(member $member){
		$shoppingCartRecord=$this->sku->getShoppingCartRecord($member);
		$data=array();
		$data['sourceType']=$this->sku->getSourceType();
		$data['sourceId']=$this->sku->getSourceId();
		$data['productType']=$this->sku->getProductType();
		$data['productId']=$this->sku->getProductId();
		$data['title']=$this->sku->getSalesUnitName();
		$data['price']=number_format(floatval($this->sku->getPrice()),2);
		$data['memberPrice']=number_format(floatval($this->sku->getLevelPrice(1)),2);
		$data['keepCout']=$this->sku->getKeepCount();
		$data['shoppingCartCount']=$shoppingCartRecord?$shoppingCartRecord->count:0;
		$data['isAllowSale']=$this->sku->isAllowSale();
		return $data;
	}
}