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
			//获取分类
			$category=category::find()->where("`id`='{$categoryId}'")->one();
			if(!$category) throw new SmartException("miss category");
			//提取子分类数据
			foreach($category->children as $child) $data[]=$child->getData();
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
			//查询query
			$sourceType=source::TYPE_SPU;
			$where="`sourceType`='{$sourceType}' AND `categoryId` IN ('{$category->id}'";
			foreach($posterities as $p) $where.=",{$p->id}";
			$where.=")";
			$query=categoryRecord::find()->where($where)->with('source');
			//获取分页数据
			$result=Yii::$app->smartPagination->getData($query,$pageSize,$pageNum);
			//组织数据
			$data=$result;
			unset($data['objs']);
			$data['spus']=array();
			foreach($result['objs'] as $recommendRecord){
				$spuExtraction=new spuExtraction($recommendRecord->source);
				$data['spus'][]=$spuExtraction->getBasicData();
			}
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(SmartException $e){$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));}	
	}
}