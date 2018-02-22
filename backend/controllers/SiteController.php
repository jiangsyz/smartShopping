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
        //根据token获取会员
		$member=tokenManagement::getManagement(Yii::$app->request->get('token',false),array(source::TYPE_MEMBER))->getOwner();
		$source=source::getSource(1,1);
		$buyingRecord=new buyingRecord(array('member'=>$member,'sourceType'=>2,'sourceId'=>1,'buyCount'=>10,'isSelected'=>true));
		var_dump($buyingRecord->getPrice());exit;
    }
}