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
use backend\models\product\spuExtraction;
class ProductController extends SmartWebController{
	//获取spu的信息
	public function actionApiGetSpuDetail(){
		try{
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取产品id
			$spuId=Yii::$app->request->get('spuId',0);
			//获取产品
			$spu=spu::find()->where("`id`='{$spuId}'")->one();
			if(!$spu) throw new SmartException("miss spu");
			//获取基础数据
			$data=$spu->getExtraction()->getBasicData();
			//获取配送类型数据
			$data['distributeType']=$spu->distributeType;
			//获取sku数据
			$data['skus']=array();
			foreach($spu->skus as $sku) $data['skus'][]=$sku->getData();
			//获取该会员对于该spu的收藏数据
			$collection=mark::getMark($member,mark::TYPE_COLLECTION,$spu->getSourceType(),$spu->id);
			$data['collectionId']=$collection?$collection->id:NULL;
			//返回详情
			$this->response(1,array('error'=>0,'data'=>$data));
		}
		catch(Exception $e){
    		$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
}