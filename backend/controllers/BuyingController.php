<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\model\source;
use backend\models\order\fastBuying;
use backend\models\order\orderAccepter;
use backend\models\orderFactory\mainOrderFactory;
use backend\models\order\orderConfirmation;
use backend\models\order\orderChecker;
use backend\models\token\tokenManagement;
use backend\models\shoppingCart\shoppingCart;
use backend\models\order\orderRecord;
class BuyingController extends SmartWebController{
	public $enableCsrfValidation=false;
	//========================================
	//通过快速购物申请创建订单
	public function actionApiApplyCreateOrderByFastBuying(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取资源类型
			$sourceType=$this->requestGet('sourceType',0);
			//获取资源id
			$sourceId=$this->requestGet('sourceId',0);
			//获取购买数量
			$buyCount=$this->requestGet('buyCount',0);
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
			$orderAccepterData['mainOrderFactory']=new mainOrderFactory(array('member'=>$member));
			$orderAccepter=new orderAccepter($orderAccepterData);
			//检查订单树的合法性
			new orderChecker(array('order'=>$orderAccepter->mainOrder));
			//构建订单确认信息处理器
			$orderConfirmation=new orderConfirmation(array('order'=>$orderAccepter->mainOrder));
			$data=$orderConfirmation->getConfirmation();
			//添加会员hash
			$data['memberHash']=$member->hash();
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//通过购物车申请创建订单
	public function actionApiApplyCreateOrderByShoppingCart(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取购物车
			$shoppingCart=new shoppingCart(array('member'=>$member));
			//构建订单受理者
			$orderAccepterData=array();
			$orderAccepterData['orderApplicant']=$shoppingCart;
			$orderAccepterData['mainOrderFactory']=new mainOrderFactory(array('member'=>$member));
			$orderAccepter=new orderAccepter($orderAccepterData);
			//检查订单树的合法性
			new orderChecker(array('order'=>$orderAccepter->mainOrder));
			//构建订单确认信息处理器
			$orderConfirmation=new orderConfirmation(array('order'=>$orderAccepter->mainOrder));
			$data=$orderConfirmation->getConfirmation();
			//添加会员hash
			$data['memberHash']=$member->hash();
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
		}
		catch(SmartException $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//通过购物车创建订单
	public function actionApiCreateOrderByShoppingCart(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestPost('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取app类型(android/ios/web)
			$appType=$this->requestPost('appType',false);
			//获取购物车
			$shoppingCart=new shoppingCart(array('member'=>$member));
			//获取购物车记录
			$shoppingCartRecords=$shoppingCart->getShoppingCartRecords();
			//构建订单受理者
			$orderAccepterData=array();
			$orderAccepterData['orderApplicant']=$shoppingCart;
			$orderAccepterData['mainOrderFactory']=new mainOrderFactory(array('member'=>$member));
			$orderAccepter=new orderAccepter($orderAccepterData);
			//检查订单树的合法性
			new orderChecker(array('order'=>$orderAccepter->mainOrder));
			//创建订单记录
			$orderRecord=$orderAccepter->mainOrder->createOrderRecord($_POST);
			//检查订单记录
			$orderRecord->checker->check();
			//记录购物车快照并清空选中的购物车
			$shoppingCartPhoto=array();
			foreach($shoppingCartRecords as $r){
				$shoppingCartPhoto[]=$r->getData();
				if($r->isSelected()) $r->delete();
			}
			$shoppingCartPhoto=json_encode($shoppingCartPhoto);
			$orderRecord->propertyManagement->addProperty('shoppingCartPhoto',$shoppingCartPhoto);
			//申请支付
            $payData=$orderRecord->payManagement->applyPay('wechat',$appType);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$payData));
		}
		catch(SmartException $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//通过快速购物创建订单
	public function actionApiCreateOrderByFastBuying(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestPost('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取资源类型
			$sourceType=$this->requestPost('sourceType',0);
			//获取资源id
			$sourceId=$this->requestPost('sourceId',0);
			//获取购买数量
			$buyCount=$this->requestPost('buyCount',0);
			//获取app类型(android/ios/web)
			$appType=$this->requestPost('appType',false);
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
			$orderAccepterData['mainOrderFactory']=new mainOrderFactory(array('member'=>$member));
			$orderAccepter=new orderAccepter($orderAccepterData);
			//检查订单树的合法性
			new orderChecker(array('order'=>$orderAccepter->mainOrder));
			//创建订单记录
			$orderRecord=$orderAccepter->mainOrder->createOrderRecord($_POST);
			//检查订单记录
			$orderRecord->checker->check();
			//申请支付
            $payData=$orderRecord->payManagement->applyPay('wechat',$appType);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$payData));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//对某一笔未支付订单申请支付
	public function actionApiApplyPay(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取app类型(android/ios/web)
			$appType=$this->requestGet('appType',false);
			//获取订单id
			$orderId=$this->requestGet('orderId',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//申请支付
            $payData=$orderRecord->payManagement->applyPay('wechat',$appType);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$payData));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
}