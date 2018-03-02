<?php
//sku数据提取器
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
//========================================
class skuExtraction{
	private $sku;
	//========================================
	public function __construct(sku $sku){$this->sku=$sku;}
	//========================================
	//获取基础数据
	public function getBasicData(){
		$data=array();
		$data['sourceType']=$this->sku->getSourceType();
		$data['sourceId']=$this->sku->getSourceId();
		$data['productType']=$this->sku->getProductType();
		$data['productId']=$this->sku->getProductId();
		$data['title']=$this->sku->getTitle();
		$data['price']=$this->sku->getPrice();
		$data['memberPrice']=$this->sku->getLevelPrice(1);
		$data['keepCout']=$this->sku->getKeepCount();
		$data['isAllowSale']=$this->sku->isAllowSale();
		return $data;
	}
}