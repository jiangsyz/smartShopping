<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\token\tokenManagement;
use backend\models\signInManagement\signInManagement;
use backend\models\identifyingCode\identifyingCodeManagement;
class StaffController extends SmartWebController{
    //员工申请登录
	public function actionApiApplySignIn(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取手机号
			$phone=Yii::$app->request->get('phone',false);
			//申请注册,获取验证码订单号
			$orderId=signInManagement::staffApplySignInByPhone($phone);
			//提交事务
			$trascation->commit();
			//返回验证码订单号
			$this->response(1,array('error'=>0,'data'=>array('orderId'=>$orderId)));
		}
		catch(Exception $e){
			//回滚
            $trascation->rollback();
    		$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//员工登录
	public function actionApiSignIn(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取验证码订单号
			$orderId=Yii::$app->request->get('orderId',false); 
			//获取验证码
			$identifyingCode=Yii::$app->request->get('identifyingCode',false);
            //注册,并获取令牌
            $token=identifyingCodeManagement::getManagement($orderId,$identifyingCode)->handle();
            //查找员工
            $staff=tokenManagement::getManagement($token->token,array(source::TYPE_STAFF))->getOwner();
            if(!$staff) throw new SmartException("miss staff");
            //提交事务
			$trascation->commit();
            //返回令牌
            $data=array();
            $data['token']=$token->token;
            $data['timeOut']=$token->getTimeOutTimestamp();
            $data['staffId']=$staff->id;
            $data['staffName']=$staff->name;
            $this->response(1,array('error'=>0,'data'=>$data));
        }
        catch(Exception $e){
            //回滚
            $trascation->rollback();
            $this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
        }
    }
}