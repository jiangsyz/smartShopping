<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\token\tokenManagement;
class MemberController extends SmartWebController{
	//获取会员信息
	public function actionApiGetInfo(){
		try{
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取数据
			$data=array();
			$data['phone']=$member->phone;
			$data['nickName']=$member->getNickName();
			$data['avatar']=$member->getAvatar();
			$data['level']=$member->getLevel();
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
			if(!$avatar) throw new SmartException("miss avatar");
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
}