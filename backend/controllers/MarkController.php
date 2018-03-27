<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\token\tokenManagement;
use backend\models\mark\mark;
class MarkController extends SmartWebController{
	//标记
	public function actionApiMark(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取标记类型
			$markType=Yii::$app->request->get('markType',0);
			//获取资源类型
			$sourceType=Yii::$app->request->get('sourceType',0);
			//获取资源id
			$sourceId=Yii::$app->request->get('sourceId',0);
			//增加标记
			$markInfo=array();
			$markInfo['memberId']=$member->id;
			$markInfo['markType']=$markType;
			$markInfo['sourceType']=$sourceType;
			$markInfo['sourceId']=$sourceId;
			$mark=mark::addObj($markInfo);
			//提交事务
			$trascation->commit();
			//返回
			$this->response(1,array('error'=>0,'data'=>$mark->id));
		}
		catch(Exception $e){
			//回滚
			$trascation->rollback();
			$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));
    	}
	}
	//========================================
	//取消标记
	public function actionApiCancelMark(){
		try{
			//开启事务
			$trascation=Yii::$app->db->beginTransaction();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取标记id
			$markId=Yii::$app->request->get('markId',0);
			//获取标记
			$where="`id`='{$markId}' AND `memberId`='{$member->id}'";
			$mark=mark::find()->where($where)->one(); if(!$mark) throw new SmartException("miss mark");
			//删除标记
			$mark->delete();
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
	//获取对于某个资源的标记者
	public function actionApiGetCollectors(){
		try{
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取标记类型
			$markType=Yii::$app->request->get('markType',0);
			//获取资源类型
			$sourceType=Yii::$app->request->get('sourceType',0);
			//获取资源id
			$sourceId=Yii::$app->request->get('sourceId',0);
			//获取标记记录
			$where="`markType`='{$markType}' AND `sourceType`='{$sourceType}' AND `sourceId`='{$sourceId}'";
			$marks=mark::find()->where($where)->with("member")->all();
			//提取数据
			$data=array();
			foreach($marks as $m){
				$memberInfo=array();
				$memberInfo['id']=$m->member->id;
				$memberInfo['avatar']=$m->member->getAvatar();
				$memberInfo['nickName']=$m->member->getNickName();
				//如果会员本人收藏了该资源,会员本人会出现在收藏者头部
				if($member->id==$m->member->id) array_unshift($data,$memberInfo); else $data[]=$memberInfo;
			}
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
		}
		catch(Exception $e){$this->response(1,array('error'=>-1,'msg'=>$e->getMessage()));}
	}
}