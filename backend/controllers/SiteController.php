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
class SiteController extends SmartWebController{
	public $enableCsrfValidation=false;
	//========================================
    public function actionIndex(){
        try{
            $command=array();
            $command['attach']="支付测试";
            $command['body']="APP支付测试";
            $command['out_trade_no']=$this->runningId;
            $command['total_fee']="1";
            //返回验证码订单号
            $data=Yii::$app->smartWechatPay->applyPay("android",$command);
            $this->response(1,array('error'=>0,'data'=>$data));
        }
        catch(Exception $e){$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));}
    }
    //========================================
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
    		$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
    }
}