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
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//echo "<pre>";print_r($member);exit;
			//获取广告位编号
			$siteNo=$this->requestGet('siteNo',false); 
			if(!$siteNo) throw new SmartException("miss siteNo");
			$data = banner::getAvailableBanner($siteNo);
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(Exception $e){$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));}
	}
}