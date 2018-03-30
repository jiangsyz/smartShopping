<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\token\tokenManagement;
use backend\models\product\spu;
use backend\models\product\sku;
use backend\models\product\skuPriceManagement;
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
			//获取sku数据
			$data['skus']=array();
			foreach($spu->skus as $sku) $data['skus'][]=$sku->getExtraction()->getBasicData($member);
			//获取该会员对于该spu的收藏数据
			$collection=mark::getMark($member,mark::TYPE_COLLECTION,$spu->getSourceType(),$spu->id);
			$data['collectionId']=$collection?$collection->id:NULL;
			//获取该会员的hash
			$data['memberHash']=$member->hash();
			//返回详情
			$this->response(1,array('error'=>0,'data'=>$data));
		}
		catch(Exception $e){
    		$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//修改sku价格
	public function actionApiUpdateSkuPrice(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=Yii::$app->request->get('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取skuId
			$skuId=Yii::$app->request->get('skuId',0);
			//获取等级
			$level=Yii::$app->request->get('level',-1);
			//获取价格
			$price=Yii::$app->request->get('price',-1);
			//获取sku(加锁)
			$sku=source::getSource(source::TYPE_SKU,$skuId,true);
			if(!$sku) throw new SmartException("miss sku");
			//获取sku价格管理器
			$skuPriceManagement=new skuPriceManagement(array('sku'=>$sku));
			//设置价格
			$skuPriceManagement->updatePrice($level,$price,$staff);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//修改sku库存
	public function actionApiUpdateSkuKeepCount(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=Yii::$app->request->get('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取skuId
			$skuId=Yii::$app->request->get('skuId',0);
			//获取库存
			$keepCount=Yii::$app->request->get('keepCount',0);
			//获取sku(加锁)
			$sku=source::getSource(source::TYPE_SKU,$skuId,true);
			if(!$sku) throw new SmartException("miss sku");
			//修改库存
			$sku->updateKeepCount(source::TYPE_STAFF,$staff->getSourceId(),$keepCount);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
}