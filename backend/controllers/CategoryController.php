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
use backend\models\product\productExtraction;
class CategoryController extends SmartWebController{
	//获取所有的顶级分类
	public function actionApiGetTopCategories(){
		try{
			$data=array();
			//根据token获取会员
			$member=tokenManagement::getManagement(Yii::$app->request->get('token',false),array(source::TYPE_MEMBER))->getOwner();
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
			$member=tokenManagement::getManagement(Yii::$app->request->get('token',false),array(source::TYPE_MEMBER))->getOwner();
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
}