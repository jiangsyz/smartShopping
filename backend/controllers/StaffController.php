<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\staff\staff;
class StaffController extends SmartWebController{
	public $enableCsrfValidation=false;
	//========================================
    //获取员工令牌
    public function actionApiGetToken(){
    	try{
			//获取手机
	    	$phone=$this->requestPost('phone',false);
	    	if(!$phone) throw new SmartException("miss phone");
	    	//获取密码
	    	$pwd=$this->requestPost('pwd',false);
	    	if(!$pwd) throw new SmartException("miss pwd");
	    	//获取员工
	    	$staff=staff::find()->where("`phone`='{$phone}' AND `locked`='0'")->one();
	    	if(!$staff) throw new SmartException("miss staff");
	    	//不能空密码
	    	if(!$staff->pwd) throw new SmartException("empty pwd");
	    	//对比密码
	    	if($staff->pwd!=$pwd) throw new SmartException("staff is locked");
	    	//创建令牌
	    	$token=$staff->createToken();			
			//获取令牌及超时时间戳
			$data=array('token'=>$token->token,'timeOut'=>$token->getTimeOutTimestamp());
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
        }
        catch(Exception $e){$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));}
    }
}