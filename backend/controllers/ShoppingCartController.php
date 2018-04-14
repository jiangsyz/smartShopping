<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\token\tokenManagement;
use backend\models\shoppingCart\shoppingCart;
use backend\models\shoppingCart\shoppingCartRecord;
use backend\models\model\source;
use backend\models\order\orderAccepter;
use backend\models\orderFactory\mainOrderFactory;
use backend\models\order\orderConfirmation;
class ShoppingCartController extends SmartWebController{
	public $enableCsrfValidation=false;
	//========================================
	//获取购物车信息
	public function actionApiGetShoppingCart(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取购物车
			$shoppingCart=new shoppingCart(array('member'=>$member));
			//构建订单受理者
			$orderAccepterData=array();
			$orderAccepterData['orderApplicant']=$shoppingCart;
			$orderAccepterData['mainOrderFactory']=new mainOrderFactory(array('member'=>$member));
			$orderAccepter=new orderAccepter($orderAccepterData);
			//构建订单确认信息处理器
			$orderConfirmation=new orderConfirmation(array('order'=>$orderAccepter->mainOrder));
			$data=$orderConfirmation->getConfirmation();
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
	//修改购物车中某个记录的数量
	public function actionApiUpdate(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取资源类型
			$sourceType=Yii::$app->request->get('sourceType',0);
			//获取售卖单元id
			$sourceId=Yii::$app->request->get('sourceId',0);
			//获取添加数量
			$count=Yii::$app->request->get('count',0);
			//查询该购买对象的购物车记录是否存在
			$where="`memberId`='{$member->id}' AND `sourceType`='{$sourceType}' AND `sourceId`='{$sourceId}'";
			$shoppingCartRecord=shoppingCartRecord::find()->where($where)->one();
			//如果存在修改数量
			if($shoppingCartRecord) 
				$shoppingCartRecord->updateObj(array('count'=>$shoppingCartRecord->count+$count));
			//不存在新增购物车记录
			else{
				$data=array();
				$data['memberId']=$member->id;
				$data['sourceType']=$sourceType;
				$data['sourceId']=$sourceId;
				$data['count']=$count;
				$shoppingCartRecord=shoppingCartRecord::addObj($data);
			}
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$shoppingCartRecord->count));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//删除购物车中多个记录
	public function actionApiDel(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->post('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取资源列表
			$sourceList=Yii::$app->request->post('sourceList',false);
			//处理资源列表
			$sourceList=json_decode($sourceList,true);
			if(!is_array($sourceList)) throw new SmartException("error sourceList");
			//逐个删除
			foreach($sourceList as $s){
				//每个sourceList元素必须包含sourceType和sourceId
				if(!isset($s['sourceType'])) throw new SmartException("miss sourceType");
				if(!isset($s['sourceId'])) throw new SmartException("miss sourceId");
				//查询该购买对象的购物车记录是否存在
				$where="`memberId`='{$member->id}' ";
				$where.="AND `sourceType`='{$s['sourceType']}' AND `sourceId`='{$s['sourceId']}'";
				$shoppingCartRecord=shoppingCartRecord::find()->where($where)->one();
				if(!$shoppingCartRecord) throw new SmartException("miss shoppingCartRecord");
				//删除
				$shoppingCartRecord->delete();
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