<?php
//员工令牌管理
namespace backend\models\token;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\staff\staff;
class staffManagement extends tokenManagement{
	//获取令牌的拥有者
	public function getOwner(){
		//获取员工(同一员工加互斥锁)
		$table=staff::tableName();
		$sql="SELECT * FROM {$table} WHERE `id`='{$this->token->data}' FOR UPDATE";
		$staff=staff::findBySql($sql)->one(); if(!$staff) throw new SmartException("miss staff");
		//员工必须处于非锁定状态
		if($staff->isLocked()) throw new SmartException("staff locked");
		//返回员工
		return $staff;
	}
	//========================================
}