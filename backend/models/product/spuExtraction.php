<?php
//spu数据提取器
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
//========================================
class spuExtraction{
	private $spu=false;
	private $cheapestSku=false;
	//========================================
	public function __construct(spu $spu){
		$this->spu=$spu;
		$this->cheapestSku=$this->spu->getCheapestSku();
		if(!$this->cheapestSku) throw new SmartException("miss cheapestSku");
	}
	//========================================
	//获取基础数据
	public function getBasicData(){
		$data=array();
		$data['sourceType']=$this->spu->getSourceType();
		$data['sourceId']=$this->spu->getSourceId();
		$data['title']=$this->spu->getProductName();
		$data['desc']=$this->spu->getProductDesc();
		$data['cover']=$this->spu->getCover();
		$data['detail']=base64_encode($this->spu->detail);
		$data['uri']=$this->spu->uri;
		$data['price']=formatPrice::formatPrice($this->cheapestSku->getLevelPrice(0));
		$data['memberPrice']=formatPrice::formatPrice($this->cheapestSku->getLevelPrice(1));
		$data['reduction']=formatPrice::formatPrice($this->cheapestSku->getReduction());
		$data['isAllowSale']=$this->spu->isAllowSale();
		$data['distributeType']=$this->spu->distributeType;
		return $data;
	}
}