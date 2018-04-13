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
    //支付
    public function actionIndex(){
        try{
            $callbackLog=false;
            //开启事务
            $trascation=Yii::$app->db->beginTransaction();
            //获取回调数据
            $data=file_get_contents('php://input');
            //解析xml
            libxml_disable_entity_loader(true);
            $data=simplexml_load_string($data,'SimpleXMLElement',LIBXML_NOCDATA);
            $data=json_decode(json_encode($data),true);
            if(!is_array($data)) throw new SmartException("data is not array");
            //记录日志
            $log=array();
            $log['runningId']=$this->runningId;
            $log['payType']='wechat';
            $log['callBackData']=json_encode($data);
            $callbackLog=payCallback::addObj($log);
            //业务结果不正确抛异常
            if(!isset($data['result_code'])) throw new SmartException("miss result_code");
            if($data['result_code']!='SUCCESS') throw new SmartException("result_code FAIL");
            //取订单号
            if(!isset($data['attach'])) throw new SmartException("miss attach");
            //取订单
            $orderRecord=orderRecord::getLockedOrderById($data['attach']);
            if(!$orderRecord) throw new SmartException("miss orderRecord");
            //支付成功
            $orderRecord->payManagement->paySuccess($this->runningId);
            //获取购买行为
            $buyingRecords=$orderRecord->buyingManagement->getBuyingList();
            //触发购买成功后的收益
            foreach($buyingRecords as $r) $r->trigger(orderBuyingRecord::EVENT_BUYING_SUCCESS);
            //修改日志中的状态信息
            $callbackLog->updateObj(array('status'=>1));
            //触发状态更改事件
            $orderRecord->statusManagement->trigger(orderStatusManagement::EVENT_STATUS_CHANGED);
            //发送通知
            $orderShowId=$orderRecord->extraction->getShowId();
            $notice=array();
            $notice['memberId']=$orderRecord->member->id;
            $notice['type']=notice::TYPE_PAY;
            $notice['content']="您的订单{$orderShowId}已经支付成功!";
            notice::addObj($notice);
            //提交事务
            $trascation->commit();
            //返回验证码订单号
            $this->response(1,array('error'=>0,'data'=>$data));
        }
        catch(Exception $e){
            //修改日志状态
            if($callbackLog) $callbackLog->updateObj(array('status'=>-1,'memo'=>$e->getMessage()));
            //回滚
            $trascation->rollback();
            $this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
        }
    }
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
    		$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
    }
}