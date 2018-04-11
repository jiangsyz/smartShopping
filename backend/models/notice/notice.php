<?php
//订单记录
namespace backend\models\notice;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\member\member;
//========================================
class notice extends SmartActiveRecord{
	const TYPE_PAY=1;
	const TYPE_VIP=2;
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
}