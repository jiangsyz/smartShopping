<?php
//spu数据提取器
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
//========================================
class spuExtraction{
	private $spu;
	//========================================
	public function __construct(spu $spu){$this->spu=$spu;}
	//========================================
	//获取基础数据
	public function getBasicData(){
		$data=array();
		$data['sourceType']=$this->spu->getSourceType();
		$data['sourceId']=$this->spu->getSourceId();
		$data['title']=$this->spu->getProductName();
		$data['desc']=$this->spu->getProductDesc();
		$data['cover']=$this->spu->getCover();
		$data['detail']=$this->spu->detail;
		$data['uri']=$this->spu->uri;
		$data['price']=number_format(floatval($this->spu->getCheapestSku()->getPrice()),2);
		$data['memberPrice']=number_format(floatval($this->spu->getCheapestSku()->getLevelPrice(1)),2);
		$data['isAllowSale']=$this->spu->isAllowSale();
		return $data;
	}
}