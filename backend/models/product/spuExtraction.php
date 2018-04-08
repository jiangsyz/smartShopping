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
	//获取详情
	public function getSpuDetail(){
		return '
		<!doctype html>
			<html>
				<head>
					<meta charset="utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" >  
					<meta name="format-detection" content="telephone=no"/>
					<style>
						body{ margin:0 auto; font-family: Arial,"微软雅黑","Helvetica Neue", Helvetica, sans-serif; color:#2c2c2c; background:#fff; padding:15px;}
						div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,input,button,textarea,select,p,span{ margin:0;padding:0;}
						img{ border:0; vertical-align:top;display: block;max-width:100% !important;}
						ul,li,ol,ul {list-style:none;}
						h1,h2,h3,h4,h5,h6 {font-weight:normal;list-style:none;}
						i{font-style: italic;}
						a { text-decoration:none;}
						a:hover {text-decoration: none}
						input{ border:0;}
						video,iframe { width:96% !important; height:220px !important;}
						input,button,textarea,select,samp,input:checked { font-family: Arial,"微软雅黑","Helvetica Neue", Helvetica, sans-serif; color:#2c2c2c;outline: 0 none; border: 0px;-webkit-appearance: none;-moz-appearance: none;appearance: none; -webkit-user-select: text; -ms-user-select: text; user-select: text;}
					</style>
				</head>
				<body>
					<article>
					<!-- 详情内容区域开始 -->
					'.$this->spu->detail.'
					<!-- 详情内容区域结束 -->
					</article>
				</body>
			</html>';
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
		$data['detail']=base64_encode($this->getSpuDetail());
		$data['uri']=$this->spu->uri;
		$data['price']=formatPrice::formatPrice($this->cheapestSku->getLevelPrice(0));
		$data['memberPrice']=formatPrice::formatPrice($this->cheapestSku->getLevelPrice(1));
		$data['reduction']=formatPrice::formatPrice($this->cheapestSku->getReduction());
		$data['isAllowSale']=$this->spu->isAllowSale();
		$data['distributeType']=$this->spu->distributeType;
		return $data;
	}
}