<?php
//订单申请单元
namespace backend\models\model;
use Yii;
//========================================
interface orderApplicant{
	//申请方类型
	const TYPE_SHOPPING_CART=1;//购物车
	const TYPE_FAST_BUYING=2;//快速购买
	//========================================
	//获取申请方类型
	public function getOrderApplicantType();
	//========================================
	//获取申请单元下的所有购买行为
	public function getBuyingRecords();
	//========================================
	//获取会员
	public function getMember();
}