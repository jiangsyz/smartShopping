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
        try{
            //开启事务
            $trascation=Yii::$app->db->beginTransaction();
            //初始化订单号
            $orders=orderRecord::find()->where("`parentId` IS NULL")->all();
            foreach($orders as $o){
                if(!$o->code)
                $o->initOrderCode();
                $o->save();
            }
            //提交事务
            $trascation->commit();
            //返回
            $this->response(1,array('error'=>0));
        }
        catch(Exception $e){
            //回滚
            $trascation->rollback();
            $this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
        }
    }
}