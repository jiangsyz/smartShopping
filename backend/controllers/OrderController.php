<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\member\member;
use backend\models\token\tokenManagement;
use backend\models\signInManagement\signInManagement;
use backend\models\identifyingCode\identifyingCodeManagement;
class OrderController extends SmartWebController{
	//获取会员订单统计
	public function actionApiGetOrderStatistics(){
		//根据token获取会员
		$token=Yii::$app->request->get('token',false);
		$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
		//组织数据
		$data=array();
		$data['unpaid']=100;
		$data['undelivered']=200;
		$data['unreceipted']=300;
		$data['customerService']=400;
		//返回
		$this->response(1,array('error'=>0,'data'=>$data));
	}
}