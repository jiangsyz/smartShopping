<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\model\source;
use backend\models\order\fastBuying;
use backend\models\order\orderAccepter;
use backend\models\orderFactory\normalOrderFactory;
use backend\models\order\orderConfirmation;
use backend\models\order\orderChecker;
use backend\models\token\tokenManagement;
class BuyingController extends SmartWebController{
	public $enableCsrfValidation=false;
	//========================================
	//通过快速购物申请创建订单
	public function actionApiApplyCreateOrderByFastBuying(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$member=tokenManagement::getManagement(Yii::$app->request->get('token',false),array(source::TYPE_MEMBER))->getOwner();
			//获取资源类型
			$sourceType=Yii::$app->request->get('sourceType',0);
			//获取资源id
			$sourceId=Yii::$app->request->get('sourceId',0);
			//获取购买数量
			$buyCount=Yii::$app->request->get('buyCount',0);
			//构建快速购买行为
			$fastBuyingData=array();
			$fastBuyingData['member']=$member;
			$fastBuyingData['sourceType']=$sourceType;
			$fastBuyingData['sourceId']=$sourceId;
			$fastBuyingData['buyCount']=$buyCount;
			$fastBuying=new fastBuying($fastBuyingData);
			//构建订单受理者
			$orderAccepterData=array();
			$orderAccepterData['orderApplicant']=$fastBuying;
			$orderAccepterData['mainOrderFactory']=new normalOrderFactory();
			$orderAccepter=new orderAccepter($orderAccepterData);
			//检查订单树的合法性
			new orderChecker(array('order'=>$orderAccepter->mainOrder));
			//构建订单确认信息处理器
			$orderConfirmation=new orderConfirmation(array('order'=>$orderAccepter->mainOrder));
			$data=$orderConfirmation->getConfirmation();
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================

}