<?php
//会员令牌管理
namespace backend\models\token;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\member\member;
class memberManagement extends tokenManagement{
	//获取令牌的拥有者
	public function getOwner(){
		//获取用户(同一用户加互斥锁)
		$table=member::tableName();
		$sql="SELECT * FROM {$table} WHERE `id`='{$this->token->data}' FOR UPDATE";
		$member=member::findBySql($sql)->one(); if(!$member) throw new SmartException("miss member");
		//用户必须处于非锁定状态
		if($member->isLocked()) throw new SmartException("member locked");
		//返回用户
		return $member;
	}
	//========================================
}