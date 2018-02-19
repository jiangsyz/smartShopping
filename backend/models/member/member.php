<?php
//会员
namespace backend\models\member;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
use backend\models\model\shop;
//========================================
class member extends source implements shop{
	//返回资源类型
	public function getSourceType(){return source::TYPE_MEMBER;}
	//========================================
	//获取昵称
	public function getNickName(){return $this->nickName;}
	//========================================
	//获取头像
	public function getAvatar(){return $this->avatar;}
	//========================================
	//获取会员等级
	public function getLevel(){
		$now=time();
		//查找在有效期内的会员等级记录
		$memberLvs=memberLv::find()->where("`start`<='{$now}' AND `end`>='{$now}'")->all();
		//找出最高的等级
		$level=0;
		foreach($memberLvs as $v) if($v->lv>$level) $level=$v->lv;
		return $level;
	}
}