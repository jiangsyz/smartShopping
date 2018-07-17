<?php
//通知
namespace backend\models\notice;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
use backend\models\member\member;
//========================================
class notice extends LogActiveRecord{
	const TYPE_PAY=1;
	const TYPE_VIP=2;
	const TYPE_ORDER=3;
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initData"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkMember"));
	}
	//========================================
	//初始化数据
	public function initData(){
		$this->createTime=time();
		$this->sendStatus=0;
		$this->sendTime=0;
	}
	//========================================
	//检查会员
	public function checkMember(){if(!$this->member) throw new SmartException("miss member");}
	//========================================
	//返回会员
	public function getMember(){return $this->hasOne(member::className(),array('id'=>'memberId'));}
	//========================================
	//返回下一跳
	public function getNextStep(){return "noticeList";}
	//========================================
	//推送
	public function push(){
		if($this->sendStatus!=0) throw new SmartException("error sendStatus");
		//先标记成出错
		$this->updateObj(array('sendStatus'=>-1,'sendTime'=>time()));
		//获取会员推送数据
		$pushUniqueInfo=explode(',',$this->member->pushUniqueId);
		if(!isset($pushUniqueInfo[0])) throw new SmartException("miss pushUniqueId");
		if(!isset($pushUniqueInfo[1])) throw new SmartException("miss appType");
		//推送
		$msg=array();
		$msg['title']=$this->content;
		$msg['msg_content']=$this->content;
		$msg['content_type']='text';
		$msg['extras']=array('nextStep'=>$this->getNextStep());
		Yii::$app->smartPush->pushByRegistrationId($pushUniqueInfo[1],$pushUniqueInfo[0],$msg);
		//标记成功
		$this->updateObj(array('sendStatus'=>1));
	}
}