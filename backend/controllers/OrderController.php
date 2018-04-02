<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\member\member;
use backend\models\token\tokenManagement;
use backend\models\order\orderRecord;
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
		$data['re']=400;
		//返回
		$this->response(1,array('error'=>0,'data'=>$data));
	}
	//========================================
	public function actionApiGetOrders(){
		try{
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取状态
			$status=Yii::$app->request->get('status',0);
			//获取每页多少条
			$pageSize=Yii::$app->request->get('pageSize',0);
			//获取当前第几页
			$pageNum=Yii::$app->request->get('pageNum',0);
			//根据不同状态取sql
			$sql=false;
			if($status=='all') $sql=$this->getAllOrders();
			if($status=='unpaid') $sql=$this->getUnpaidOrders();
			if(!$sql) throw new SmartException("error status");
			//查询query
			$query=orderRecord::findBySql($sql);
			//获取分页数据
			$result=Yii::$app->smartPagination->getData($query,$pageSize,$pageNum);
			//组织数据
			$data=$result;
			unset($data['objs']);
			$data['orders']=array();
			foreach($result['objs'] as $order){
				$data['orders'][]=$order->extraction->getBasicData();
			}
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(Exception $e){$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));}
	}
	//========================================
	//获取待支付订单
	public function getUnpaidOrders(){
		$table=orderRecord::tableName();
		return "SELECT * FROM {$table} WHERE `parentId` is NULL AND `payStatus`='0' AND `cancelStatus`='0' ORDER BY `createTime` DESC";
	}
	//========================================
	//获取全部订单
	public function getAllOrders(){
		$table=orderRecord::tableName();
		return "SELECT * FROM {$table} WHERE `parentId` is NULL ORDER BY `createTime` DESC";
	}
}