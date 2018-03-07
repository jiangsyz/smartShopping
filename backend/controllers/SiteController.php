<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use backend\models\model\source;
use backend\models\product\spu;
use backend\models\product\sku;
use backend\models\product\virtualItem;
use backend\models\orderFactory\buyingRecord;
use backend\models\token\tokenManagement;
class SiteController extends SmartWebController{
    public function actionIndex(){
        $sku=sku::find()->where("`id`='1'")->one();
        var_dump($sku->getPrice());
    }
}