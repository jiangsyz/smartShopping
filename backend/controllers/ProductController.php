<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\token\tokenManagement;
use backend\models\product\spu;
use backend\models\model\source;
use backend\models\mark\mark;
class ProductController extends SmartWebController{
	//获取spu的信息
	public function actionApiGetSpuDetail(){
		try{
			//根据token获取会员
			$member=tokenManagement::getManagement(Yii::$app->request->get('token',false),array(source::TYPE_MEMBER))->getOwner();
			//获取产品id
			$spuId=Yii::$app->request->get('spuId',0);
			//获取产品
			$spu=spu::find()->where("`id`='{$spuId}'")->one(); if(!$spu) throw new SmartException("miss spu");
			//获取产品详情
			$data=$spu->getData(array('title','desc','cover','detail','uri'));
			//获取资源类型和资源id
			$data['sourceType']=$spu->getSourceType();
			$data['sourceId']=$spu->getSourceId();
			//获取销售价
			$data['price']=$spu->getCheapestSku()->getPrice();
			//获取会员价
			$data['memberPrice']=$spu->getCheapestSku()->getLevelPrice(1);
			//获取sku
			$data['skus']=array();
			foreach($spu->skus as $sku) $data['skus'][]=$sku->getData();
			//获取收藏信息
			$collection=mark::getMark($member,mark::TYPE_COLLECTION,$spu->getSourceType(),$spu->getSourceId());
			$data['collectionId']=$collection?$collection->id:NULL;
			//获取某个资源的收藏会员
			$data['collectors']=array();
			$collectors=mark::getMarkers(mark::TYPE_COLLECTION,$spu->getSourceType(),$spu->getSourceId());
			foreach($collectors as $collector) $data['collectors'][]=$collector->getAvatar();
			//返回详情
			$this->response(1,array('error'=>0,'data'=>$data));
		}
		catch(Exception $e){
    		$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
}