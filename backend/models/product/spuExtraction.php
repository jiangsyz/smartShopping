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
		$i=0;
		$images=array();
		$data=array();
		$detail=$this->spu->detail;
		//先找出所有带<a>标签的图片
        $ruleA='%<a.*?>(.*?)</a>%si';
        preg_match_all($ruleA,$detail,$matchA);
        foreach($matchA[0] as $a){
        	//提取href
        	$ruleHref='/<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/i';
        	preg_match_all($ruleHref,$a,$matchHref);
        	$href=$matchHref[2][0];
        	$images[$i]['href']=$href;
        	//提取src
        	$ruleSrc="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";
        	preg_match_all($ruleSrc,$a,$matchSrc,PREG_PATTERN_ORDER);
        	$images[$i]['src']=$matchSrc[1][0];
        	//更新索引
        	$i++;
        }
        //再找出所有不带<a>标签的图片
        $detail=preg_replace("/<(a.*?)>(.*?)<(\/a.*?)>/si","",$detail);
        $ruleSrc="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";
        preg_match_all($ruleSrc,$detail,$matchSrc,PREG_PATTERN_ORDER);
        foreach($matchSrc[1] as $src) $images[$i++]=array('href'=>'','src'=>$src);
        //根据图片在原文中的出现位置排序
        foreach($images as $image) $data[strpos($this->spu->detail,$image['src'])]=$image;
        //返回提取数据
        return $data;
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
		$data['detail']=base64_encode(json_encode($this->getSpuDetail()));
		$data['uri']=$this->spu->uri;
		$data['price']=formatPrice::formatPrice($this->cheapestSku->getLevelPrice(0));
		$data['memberPrice']=formatPrice::formatPrice($this->cheapestSku->getLevelPrice(1));
		$data['reduction']=formatPrice::formatPrice($this->cheapestSku->getReduction());
		$data['isAllowSale']=$this->spu->isAllowSale();
		$data['distributeType']=$this->spu->distributeType;
		return $data;
	}
}