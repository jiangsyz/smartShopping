<?php
//人
namespace backend\models\model;
use Yii;
//========================================
interface person{
	//获取昵称
	public function getNickName();
	//获取头像
	public function getAvatar();
}