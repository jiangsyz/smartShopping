<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\model\source;
use backend\models\product\spu;
use backend\models\product\sku;
use backend\models\product\virtualItem;
use backend\models\orderFactory\buyingRecord;
use backend\models\member\member;
use backend\models\token\tokenManagement;
use backend\models\order\orderRecord;
use backend\models\pay\payCallback;
use backend\models\order\orderBuyingRecord;
use backend\models\notice\notice;
use backend\models\order\orderStatusManagement;
class SiteController extends SmartWebController{
	public $enableCsrfValidation=false;
	//========================================
    //获取七牛令牌
    public function actionApiGetQiNiuToken(){
    	try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取令牌
			$token=Yii::$app->smartQiNiu->getToken();
			//提交事务
			$trascation->commit();
			//返回验证码订单号
			$this->response(1,array('error'=>0,'data'=>$token));
		}
		catch(Exception $e){
			//回滚
            $trascation->rollback();
    		$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
    }
}