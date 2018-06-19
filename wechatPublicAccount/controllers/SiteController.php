<?php
namespace wechatPay\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
class SiteController extends SmartWebController{
    public $enableCsrfValidation=false;
    //========================================
    //
    public function actionIndex(){echo 123;}
}