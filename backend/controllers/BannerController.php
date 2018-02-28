<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\model\source;
use backend\models\token\tokenManagement;
use backend\models\banner\banner;
class BannerController extends SmartWebController{
	//获取幻灯片
	public function actionApiGetBanner(){
		try{
			$data=array();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取广告位编号
			$siteNo=Yii::$app->request->get('siteNo',false); 
			if(!$siteNo) throw new SmartException("miss siteNo");
			//获取推荐内容
			$banners=banner::find()->where("`siteNo`='{$siteNo}'")->orderBy("`sort` ASC")->all();
			//组织数据
			foreach($banners as $b) $data[]=$b->getData();
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(Exception $e){$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));}	
	}
}