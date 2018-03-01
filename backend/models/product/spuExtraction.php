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
		$data['price']=$this->spu->getCheapestSku()->getPrice();
		$data['memberPrice']=$this->spu->getCheapestSku()->getLevelPrice(1);
		return $data;
	}
}