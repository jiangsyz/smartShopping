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
use backend\models\order\orderBuyingRecord;
use backend\models\notice\notice;
class TaskController extends SmartWebController{
	//检查订单支付超时
	public function actionApiCheckPayTimeOutOrder(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//取一个最旧的未支付的订单id
			$table=orderRecord::tableName();
			$sql=
			"
					SELECT 
						`id` 
					FROM 
						{$table} 
					WHERE 
						`parentId` is NULL 
						AND 
						`payStatus`='0' 
						AND 
						`cancelStatus`='0' 
					ORDER BY `createTime` ASC LIMIT 1;
			";
			$row=Yii::$app->db->createCommand($sql)->queryOne();
			//没取到id(这里虽然抛异常,但也不算错,没有需要处理而已)
			if(!isset($row['id'])) throw new SmartException("miss id");
			//加锁取订单
			$orderRecord=orderRecord::getLockedOrderById($row['id']);
			if(!$orderRecord) throw new SmartException("miss order {$row['id']}");
			//检查超时
			$orderRecord->payManagement->canOrderPay();
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$orderRecord->id));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//订单超时/取消反库存
	public function actionApiBackKeepCount(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取一个最旧的支付超时或取消的且未退库存的订单
			$table=orderRecord::tableName();
			$sql=
			"
					SELECT 
						`id` 
					FROM 
						{$table} 
					WHERE 
						`parentId` is NULL 
						AND 
						(`payStatus`='-1' OR `cancelStatus`='1') 
						AND 
						`backKeepCountStatus`='0' 
					ORDER BY `createTime` ASC LIMIT 1;
			";
			$row=Yii::$app->db->createCommand($sql)->queryOne();
			//没取到id(这里虽然抛异常,但也不算错,没有需要处理而已)
			if(!isset($row['id'])) throw new SmartException("miss id");
			//加锁取订单
			$orderRecord=orderRecord::getLockedOrderById($row['id']);
			if(!$orderRecord) throw new SmartException("miss order {$row['id']}");
			//返库存
			$orderRecord->cancelManagement->backKeepCount();
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$orderRecord->id));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//推送
	public function actionApiPushNotice(){
		try{
			$notice=notice::find()->where("`sendStatus`='0'")->orderBy("createTime ASC")->one();
			if(!$notice) throw new SmartException("miss notice");
			$notice->push();
			//返回
			$this->response(1,array('error'=>0,'data'=>$notice->id));
		}
		catch(Exception $e){
			//回滚
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
}