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
class TestController extends SmartWebController{
    public function actionIndex(){echo time();}
    //========================================
    public function actionTest(){
    	$get=array("phone"=>"13402155751","requestTime"=>time());
    	$get['requestTime']=(string)$get['requestTime'];
    	ksort($get);
    	var_dump(json_encode($get)."^ZS2018LCJ");
    	$signature=md5(json_encode($get)."^ZS2018LCJ");
    	$url="http://localhost/smartShopping/backend/web/index.php?r=member/api-get-token-by-phone&phone={$get['phone']}&requestTime={$get['requestTime']}&signature={$signature}";
    	echo $url;
    }
}