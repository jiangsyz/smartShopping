<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\member\member;
use backend\models\token\tokenManagement;
use backend\models\signInManagement\signInManagement;
use backend\models\identifyingCode\identifyingCodeManagement;
class MemberController extends SmartWebController{
	//通过手机拿令牌
    public function actionApiGetTokenByPhone(){
		try{
			//获取手机
			$phone=Yii::$app->request->get('phone',"");
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
        catch(Exception $e){$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));}
    }
	//========================================
    //通过手机号申请登陆
	public function actionApiApplySignInByPhone(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取手机号
			$phone=Yii::$app->request->get('phone',false);
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
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
		}
	}
	//========================================
	//会员注册或登录
	public function actionApiSignUpOrSignIn(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//获取验证码订单号
			$orderId=Yii::$app->request->get('orderId',false); 
			//获取验证码
			$identifyingCode=Yii::$app->request->get('identifyingCode',false);
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
            $this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
        }
    }
	//========================================
	//获取会员信息
	public function actionApiGetInfo(){
		try{
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取vip信息
			$vip=$member->getVipInfo();
			//获取数据
			$data=array();
			$data['phone']=$member->phone;
			$data['nickName']=$member->getNickName();
			$data['avatar']=$member->getAvatar();
			$data['level']=$vip==NULL?0:$vip->lv;
			$data['vipEnd']=$vip==NULL?0:$vip->end;
			$data['pushUniqueId']=$member->pushUniqueId;
			$data['customServiceUniqueId']=$member->customServiceUniqueId;
			$data['hash']=$member->hash();
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
		}
		catch(Exception $e){$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));}
	}
	//========================================
	//添加会员头像
	public function actionApiUploadAvatar(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取图像
			$avatar=Yii::$app->request->get('avatar',false);
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
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//添加会员昵称
	public function actionApiUploadNickname(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取昵称
			$nickName=Yii::$app->request->get('nickName',false);
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
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//添加会员推送平台id
	public function actionApiUploadPushUniqueId(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取平台id
			$pushUniqueId=Yii::$app->request->get('pushUniqueId',false);
			//上传昵称
			$member->uploadPushUniqueId($pushUniqueId);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$member->pushUniqueId));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//添加会员客服平台id
	public function actionApiUploadCustomServiceUniqueId(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取平台id
			$customServiceUniqueId=Yii::$app->request->get('customServiceUniqueId',false);
			//上传昵称
			$member->uploadCustomServiceUniqueId($customServiceUniqueId);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$member->customServiceUniqueId));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
}