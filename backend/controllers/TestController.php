<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use backend\models\model\source;
use backend\models\product\spu;
use backend\models\product\sku;
use backend\models\product\virtualItem;
use backend\models\orderFactory\buyingRecord;
use backend\models\member\member;
use backend\models\token\tokenManagement;
use backend\models\notice\notice;
use yii\base\Exception;
use backend\models\order\orderRecord;
class TestController extends SmartWebController{
    public function actionIndex(){echo time();}
    //========================================
    public function actionTest(){
		$appId1=Yii::$app->params["app1"]["appId"];
		$appSecret2=Yii::$app->params["app1"]["appSecret"];
		$appId2=Yii::$app->params["app2"]["appId"];
		$appSecret2=Yii::$app->params["app2"]["appSecret"];

		$data=array();
		$data['openid']='oy1Thsj7A0EQ9i8GWz9KtnZWygRI';
		$data['templateId']='5fT3a_mLXelmzODom-wQAr5mUsPNP5K68me7khqs_os';
		$data['msgData']=array();
		$data['msgData']['first']=array("value"=>"恭喜你购买成功！","color"=>"#173177");
		$data['msgData']['keyword1']=array("value"=>"1","color"=>"#173177");
		$data['msgData']['keyword2']=array("value"=>date("Y_m_d_H_i_s",time()),"color"=>"#173177");
		$data['msgData']['keyword3']=array("value"=>"100.11","color"=>"#173177");
		$data['msgData']['keyword4']=array("value"=>"微信支付","color"=>"#173177");
		$data['msgData']['remark']=array("value"=>"感谢您的惠顾","color"=>"#173177");
		$data['miniAppId']=$appId1;
		$data['miniPagepath']='';

		Yii::$app->smartWechat->pushTemplateMsg($appId2,$appSecret2,$data);
    }
}