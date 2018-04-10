<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\token\tokenManagement;
use backend\models\category\category;
use backend\models\category\categoryRecord;
use backend\models\product\spuExtraction;
use backend\models\product\spu;
class CategoryController extends SmartWebController{
	//获取所有的顶级分类
	public function actionApiGetTopCategories(){
		try{
			$data=array();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取顶级分类
			$categories=category::getTopCategories();
			//组织数据
			foreach($categories as $category) $data[]=$category->getData();
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(SmartException $e){$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));}
	}
	//========================================
	//获取一个分类的子分类
	public function actionApiGetChildren(){
		try{
			$data=array();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取分类id
			$categoryId=Yii::$app->request->get('categoryId',0);
			//获取是否包含父分类
			$includeSelf=Yii::$app->request->get('includeSelf',0);
			//获取分类
			$category=category::find()->where("`id`='{$categoryId}'")->one();
			if(!$category) throw new SmartException("miss category");
			//提取子分类数据
			foreach($category->children as $child) $data[]=$child->getData();
			//如果包含父分类,对数据进行处理后入栈
			$parentData=$category->getData();
			$parentData['name']='全部';
			if($includeSelf==1) array_unshift($data,$parentData);
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(SmartException $e){$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));}
	}
	//========================================
	//获取某个分类下的所有spu(递归搜索后代分类下的spu)
	public function actionApiGetSpu(){
		try{
			$data=array();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取分类id
			$categoryId=Yii::$app->request->get('categoryId',0);
			//获取每页多少条
			$pageSize=Yii::$app->request->get('pageSize',0);
			//获取当前第几页
			$pageNum=Yii::$app->request->get('pageNum',0);
			//获取分类
			$category=category::find()->where("`id`='{$categoryId}'")->one();
			if(!$category) throw new SmartException("miss category");
			//获取后代分类
			$posterities=$category->getPosterities();
			//获取sql
			$sourceType=source::TYPE_SPU;
			$cTable=categoryRecord::tableName();
			$sTable=spu::tableName();
			$categoryIds="'{$category->id}'";
			foreach($posterities as $p) $categoryIds.=",{$p->id}";
			$sql="
				SELECT {$cTable}.* 
					FROM 
						{$cTable} 
					JOIN 
						{$sTable}
					ON 
						{$cTable}.`sourceId`={$sTable}.`id` 
					WHERE 
						{$cTable}.`sourceType`='{$sourceType}' AND 
						{$cTable}.`categoryId` IN ({$categoryIds}) AND
						{$sTable}.`closed`='0' AND
						{$sTable}.`locked`='0'
				";
			//获取query
			$query=categoryRecord::findBySql($sql)->with('source');
			//获取分页数据
			$result=Yii::$app->smartPagination->getData($query,$pageSize,$pageNum);
			//组织数据
			$data=$result;
			unset($data['objs']);
			$data['spus']=array();
			foreach($result['objs'] as $categoryRecord){
				$spuExtraction=new spuExtraction($categoryRecord->source);
				$data['spus'][]=$spuExtraction->getBasicData();
			}
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(SmartException $e){$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));}	
	}
}