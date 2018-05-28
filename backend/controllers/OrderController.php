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
use backend\models\order\orderStatusManagement;
class OrderController extends SmartWebController{
	public $enableCsrfValidation=false;
	//========================================
	//获取会员订单统计
	public function actionApiGetOrderStatistics(){
		//根据token获取会员
		$token=$this->requestGet('token',false);
		$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
		//组织数据
		$data=array();
		$data['unpaid']=orderRecord::findBySql($this->getUnpaidOrders($member))->count();
		$data['undelivered']=orderRecord::findBySql($this->getUndeliveredOrders($member))->count();
		$data['unreceipted']=orderRecord::findBySql($this->getUnreceiptedOrders($member))->count();
		$data['refunding']=orderRecord::findBySql($this->getRefundingOrders($member))->count();
		//返回
		$this->response(1,array('error'=>0,'data'=>$data));
	}
	//========================================
	//获取订单
	public function actionApiGetOrders(){
		try{
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取状态
			$status=$this->requestGet('status',0);
			//获取每页多少条
			$pageSize=$this->requestGet('pageSize',0);
			//获取当前第几页
			$pageNum=$this->requestGet('pageNum',0);
			//根据不同状态取sql
			$sql=false;
			if($status=='all') $sql=$this->getAllOrders($member);
			if($status=='unpaid') $sql=$this->getUnpaidOrders($member);
			if($status=='undelivered') $sql=$this->getUndeliveredOrders($member);
			if($status=='unreceipted') $sql=$this->getUnreceiptedOrders($member);
			if($status=='refunding') $sql=$this->getRefundingOrders($member);
			if(!$sql) throw new SmartException("error status");
			//查询query
			$query=orderRecord::findBySql($sql);
			//获取分页数据
			$class=orderRecord::className();
			$result=Yii::$app->smartPagination->getDataBySql($class,$sql,$pageSize,$pageNum);
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
    	catch(Exception $e){$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));}
	}
	//========================================
	//获取全部订单
	public function getAllOrders(member $m){
		$table=orderRecord::tableName();
		return 
		"
			SELECT * FROM {$table} 
			WHERE 
				`memberId`='{$m->id}' 
				AND 
				`parentId` is NULL 
			ORDER BY `createTime` DESC
		";
	}
	//========================================
	//获取待支付订单
	public function getUnpaidOrders(member $m){
		$table=orderRecord::tableName();
		return 
		"
			SELECT * FROM {$table} 
			WHERE 
				`memberId`='{$m->id}' 
				AND 
				`parentId` is NULL 
				AND 
				`payStatus`='0' 
				AND 
				`cancelStatus`='0' 
				AND 
				`closeStatus`='0' 
				AND 
				`deliverStatus`='0' 
				AND 
				`refundingStatus`='0' 
				AND 
				`finishStatus`='0' 
			ORDER BY `createTime` DESC
		";
	}
	//========================================
	//获取待发货订单
	public function getUndeliveredOrders(member $m){
		$table=orderRecord::tableName();
		return 
		"
			SELECT * FROM {$table} 
			WHERE 
				`memberId`='{$m->id}' 
				AND 
				`parentId` is NULL 
				AND 
				`payStatus`='1' 
				AND 
				`cancelStatus`='0' 
				AND 
				`closeStatus`='0' 
				AND 
				`deliverStatus`='0' 
				AND 
				`refundingStatus`='0' 
				AND 
				`finishStatus`='0' 
			ORDER BY `createTime` DESC
		";
	}
	//========================================
	//获取待收货订单
	public function getUnreceiptedOrders(member $m){
		$table=orderRecord::tableName();
		return 
		"
			SELECT * FROM {$table} 
			WHERE 
				`memberId`='{$m->id}' 
				AND 
				`parentId` is NULL 
				AND 
				`payStatus`='1' 
				AND 
				`cancelStatus`='0' 
				AND 
				`closeStatus`='0' 
				AND 
				`deliverStatus` IN('1','2') 
				AND 
				`refundingStatus`='0' 
				AND 
				`finishStatus`='0' 
			ORDER BY `createTime` DESC
		";
	}
	//========================================
	//获取售后订单
	public function getRefundingOrders(member $m){
		$table=orderRecord::tableName();
		return 
		"
			SELECT * FROM {$table} 
			WHERE 
				`memberId`='{$m->id}' 
				AND 
				`parentId` is NULL 
				AND 
				`refundingStatus`='1' 
			ORDER BY `createTime` DESC
		";
	}
	//========================================
	//取消订单
	public function actionApiCancel(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取订单id
			$orderId=$this->requestGet('orderId',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//判断订单是否属于当前会员
			if($orderRecord->memberId!=$member->id) throw new SmartException("error memberId");
			//取消订单
			$orderRecord->cancelManagement->cancel();
			//触发状态更改事件
            $orderRecord->statusManagement->trigger(orderStatusManagement::EVENT_STATUS_CHANGED);
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
	//========================================
	//关闭订单
	public function actionApiClose(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=$this->requestGet('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取订单id
			$orderId=$this->requestGet('orderId',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//取消订单
			$orderRecord->cancelManagement->close($staff);
			//触发状态更改事件
            $orderRecord->statusManagement->trigger(orderStatusManagement::EVENT_STATUS_CHANGED);
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
	//========================================
	//获取订单详情
	public function actionApiGetDetail(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取订单id
			$orderId=$this->requestGet('orderId',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//判断订单是否属于当前会员
			if($orderRecord->memberId!=$member->id) throw new SmartException("error memberId");
			//获取订单详情
			$data=$orderRecord->extraction->getDetail();
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
	//确认收货
	public function actionApiReceipted(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取订单id
			$orderId=$this->requestGet('orderId',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//判断订单是否属于当前会员
			if($orderRecord->memberId!=$member->id) throw new SmartException("error memberId");
			//确认收货
			$orderRecord->statusManagement->receipted();
			//触发状态更改事件
            $orderRecord->statusManagement->trigger(orderStatusManagement::EVENT_STATUS_CHANGED);
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
	//========================================
	//修改地址
	public function actionApiChangeAddress(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取订单id
			$orderId=$this->requestGet('orderId',0);
			//获取地址id
			$addressId=$this->requestGet('addressId',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//判断订单是否属于当前会员
			if($orderRecord->memberId!=$member->id) throw new SmartException("error memberId");
			//修改地址
			$orderRecord->addressManagement->changeAddress($addressId);
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
	//========================================
	//修改订单的商品价格
	public function actionApiChangePrice(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=$this->requestPost('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();	
			//获取订单id
			$orderId=$this->requestPost('orderId',0);
			//获取备注
			$memo=$this->requestPost('memo',"");
			//获取修改后的价格
			$price=$this->requestPost('price',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//修改商品价格
			$orderRecord->payManagement->changePrice($staff,$price,$memo);
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
	//========================================
	//修改订单的商品价格
	public function actionApiChangeFreight(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=$this->requestPost('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();	
			//获取订单id
			$orderId=$this->requestPost('orderId',0);
			//获取备注
			$memo=$this->requestPost('memo',"");
			//获取修改后的价格
			$freight=$this->requestPost('freight',0);
			//获取订单
			$orderRecord=orderRecord::getLockedOrderById($orderId);
			if(!$orderRecord) throw new SmartException("miss orderRecord");
			//修改商品运费
			$orderRecord->payManagement->changeFreight($staff,$freight,$memo);
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