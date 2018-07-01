<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\member\member;
use backend\models\member\memberLv;
use backend\models\token\tokenManagement;
use backend\models\signInManagement\signInManagement;
use backend\models\identifyingCode\identifyingCodeManagement;
use backend\models\product\formatPrice;
class MemberController extends SmartWebController{
	public $enableCsrfValidation=false;
    //========================================
	//通过手机拿令牌
    public function actionApiGetTokenByPhone(){
		try{
			//获取手机
			$phone=$this->requestGet('phone',"");
			//获取会员
			$member=member::find()->where("`phone`='{$phone}'")->one();
			//不是会员
			if(!$member) throw new SmartException("miss member");
			//创建会员令牌
			$token=$member->createToken();
			//获取令牌及超时时间戳
			$data=array('token'=>$token->token,'timeOut'=>$token->getTimeOutTimestamp());
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
        }
        catch(Exception $e){$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));}
    }
	//========================================
    //通过手机号申请登陆
	public function actionApiApplySignInByPhone(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取手机号
			$phone=$this->requestGet('phone',false);
			//申请注册,获取验证码订单号
			$orderId=signInManagement::memberApplySignInByPhone($phone);
			//提交事务
			$trascation->commit();
			//返回验证码订单号
			$this->response(1,array('error'=>0,'data'=>array('orderId'=>$orderId)));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
		}
	}
	//========================================
	//会员注册或登录
	public function actionApiSignUpOrSignIn(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取验证码订单号
			$orderId=$this->requestGet('orderId',false); 
			//获取验证码
			$identifyingCode=$this->requestGet('identifyingCode',false);
            //注册,并获取令牌
            $token=identifyingCodeManagement::getManagement($orderId,$identifyingCode)->handle();
            //提交事务
            $trascation->commit();
            //返回令牌
            $this->response(1,array('error'=>0,'data'=>$token->getInfo()));
        }
        catch(SmartException $e){
            //回滚
            $trascation->rollback();
            $this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
        }
    }
	//========================================
	//获取会员信息
	public function actionApiGetInfo(){
		try{
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取vip信息
			$vipData=memberLv::getVipData($member);
			//获取数据
			$data=array();
			$data['memberId']=$member->id;
			$data['phone']=$member->phone;
			$data['nickName']=$member->getNickName();
			$data['avatar']=$member->getAvatar();
			$data['level']=$vipData['lv'];
			$data['vipEnd']=$vipData['end'];
			$data['pushUniqueId']=$member->pushUniqueId;
			$data['customServiceUniqueId']=rand(0,1)==0?"77043":"77484";
			$data['reduction']=$member->getReduction();
			$data['expectedReduction']=Yii::$app->params['expectedReduction'];
			$data['memberHash']=$member->hash();
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
		}
		catch(Exception $e){$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));}
	}
	//========================================
	//添加会员头像
	public function actionApiUploadAvatar(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取图像
			$avatar=$this->requestGet('avatar',false);
			//上传头像
			$member->uploadAvatar($avatar);
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
	//添加会员昵称
	public function actionApiUploadNickname(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取昵称
			$nickName=$this->requestGet('nickName',false);
			//上传昵称
			$member->uploadNickName($nickName);
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
	//添加会员推送平台id
	public function actionApiUploadPushUniqueId(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取平台id
			$pushUniqueId=$this->requestGet('pushUniqueId',false);
			if(!$pushUniqueId) throw new SmartException("推送uid缺失",-2);
			//上传推送平台id
			$member->updateObj(array('pushUniqueId'=>$pushUniqueId));
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$member->pushUniqueId));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//清除会员推送平台id
	public function actionApiClearPushUniqueId(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//清空推送平台id
			$member->updateObj(array('pushUniqueId'=>NULL));
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
	//关闭某个时间段的vip
	public function actionApiCloseVip(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取员工
			$token=$this->requestPost('token',false);
			$staff=tokenManagement::getManagement($token,array(source::TYPE_STAFF))->getOwner();
			//获取会员等级id
			$memberLvId=$this->requestPost('memberLvId',false); 
			if(!$memberLvId) throw new SmartException("miss memberLvId");
			//获取备注
			$memo=$this->requestPost('memo',""); 
			if(!$memo) throw new SmartException("miss memo");
			//获取需要关闭的会员等级
			$table=memberLv::tableName();
			$sql="SELECT * FROM {$table} WHERE `id`='{$memberLvId}' AND `closed`='0' FOR UPDATE";
			$memberLv=memberLv::findBySql($sql)->one();
			if(!$memberLv) throw new SmartException("miss memberLv");
			//组织备注
			$closedMemo=array();
			$closedMemo['handlerType']=$staff->getSourceType();
			$closedMemo['handlerId']=$staff->getSourceId();
			$closedMemo['memo']=$memo;
			$closedMemo['time']=time();
			$closedMemo=json_encode($closedMemo);
			//关闭vip
			$memberLv->updateObj(array('closed'=>1,'closedMemo'=>$closedMemo));
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