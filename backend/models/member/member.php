<?php
//会员
namespace backend\models\member;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
use backend\models\model\person;
//========================================
class member extends source implements person{
	//返回资源类型
	public function getSourceType(){return source::TYPE_MEMBER;}
	//========================================
	//获取昵称
	public function getNickName(){return $this->nickName;}
	//========================================
	//获取头像
	public function getAvatar(){return $this->avatar;}
}