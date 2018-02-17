<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use backend\models\model\source;
use backend\models\product\spu;
use backend\models\product\virtualItem;
use backend\models\token\tokenManagement;
class SiteController extends SmartWebController{
    public function actionIndex(){
        //根据token获取会员
		$member=tokenManagement::getManagement(Yii::$app->request->get('token',false),array(source::TYPE_MEMBER))->getOwner();
		var_dump($member->getLevel());
    }
}