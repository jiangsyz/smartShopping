<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\console\SmartDaemonController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\product\spu;
use backend\models\product\sku;
use backend\models\product\virtualItem;
use backend\models\orderFactory\buyingRecord;
use backend\models\member\member;
use backend\models\token\tokenManagement;
use backend\models\notice\notice;
use backend\models\order\orderRecord;
class OrderController extends SmartDaemonController{
	//订单支付超时检测
    public function actionCheckPayTimeout(){
    	$this->begin();
    	//循环处理
    	while(1){
    		try{
				//开启事务
				$trascation=Yii::$app->db->beginTransaction();
				//获取未支付的订单id
				$table=orderRecord::tableName();
				$sql=
				"
				SELECT `id` FROM {$table} 
				WHERE 
					`parentId` is NULL 
					AND 
					`payStatus`='0' 
					AND 
					`cancelStatus`='0' 
					AND 
					`closeStatus`='0';
				";
				$rows=Yii::$app->db->createCommand($sql)->queryAll();
				//循环检查超时
				foreach($rows as $row){
					//获取订单
					$orderRecord=orderRecord::getLockedOrderById($row['id']);
					//检查超时
					$orderRecord->payManagement->canOrderPay();
					//记录日志
					Yii::$app->smartLog->consoleLog("check order {$orderRecord->id}");
				}
				//提交事务
				$trascation->commit();
    		}
	    	catch(Exception $e){$trascation->rollback();}
	    	//休息一下
			$this->sleep();
			//报告存活
			$this->alive();
    	}
    }
    //========================================
	//订单支付超时或取消或关闭返库存
	public function actionBackKeepCount(){
		$this->begin();
    	//循环处理
    	while(1){
    		try{
				//开启事务
				$trascation=Yii::$app->db->beginTransaction();
				//获取支付超时或取消或关闭的订单id
				$table=orderRecord::tableName();
				$sql=
				"
				SELECT `id` FROM {$table} 
				WHERE 
					`parentId` is NULL 
					AND 
					(`payStatus`='-1' OR `cancelStatus`='1' OR `closeStatus`='1') 
					AND 
					`backKeepCountStatus`='0';
				";
				$rows=Yii::$app->db->createCommand($sql)->queryAll();
				//循环检返库存
				foreach($rows as $row){
					//加锁取订单
					$orderRecord=orderRecord::getLockedOrderById($row['id']);
					//返库存
					$orderRecord->cancelManagement->backKeepCount();
					//记录日志
					Yii::$app->smartLog->consoleLog("check order {$orderRecord->id}");
				}
				//提交事务
				$trascation->commit();
			}
			catch(Exception $e){$trascation->rollback();}	
	    	//休息一下
			$this->sleep();
			//报告存活
			$this->alive();
    	}
	}
	//========================================
}
?>