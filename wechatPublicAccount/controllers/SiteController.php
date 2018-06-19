<?php
namespace wechatPublicAccount\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
class SiteController extends SmartWebController{
    public $enableCsrfValidation=false;
    //========================================
    public function actionIndex(){
    	header('content-type:text');
    	$token='zs2018WLJFT';
    	$echostr=$this->requestGet('echostr','14304277057741240954');
    	$signature=$this->requestGet('signature','f25d294bdffe7e5d9e702e5ce991e08674d99ce8');
		$timestamp=$this->requestGet('timestamp','1529391656');
		$nonce=$this->requestGet('nonce','3289602810');
		$array=array($nonce,$timestamp,$token);
		sort($array);
		$array=implode($array);
		$str=sha1($array);
		if($str==$signature && $echostr){
			Yii::$app->smartLog->debugLog($echostr);
			ob_clean();
        	echo $echostr;
        	exit;
    	}
    	else{
    		Yii::$app->smartLog->debugLog("123");
        	exit;
    	}
    }
}