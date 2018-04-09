<?php
//会员等级
namespace backend\models\member;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\order\orderBuyingRecord;
//========================================
class memberLv extends SmartActiveRecord{
	//获取会员的vip详情
	public static function getVipInfo(member $member){
		$now=time();
		//查找在有效期内的会员等级记录
		$where="`start`<='{$now}' AND `end`>='{$now}' AND `memberId`='{$member->id}'";
		$memberLvs=self::find()->where($where)->all();
		//以等级高的为准,等级相同以最晚截至时间为准
		$vip=NULL;
		foreach($memberLvs as $v){
			if(!$vip) $vip=$v;
			if($v->lv>$vip->lv) $vip=$v;
			if($v->lv==$vip->lv && $v->end>$vip->end) $vip=$v;
		}
		return $vip;
	}
	//========================================
	//开通vip
	public static function addVip(orderBuyingRecord $r,$data){
		//校验
		if(!isset($data['lv'])) throw new SmartException("benefitDetail miss lv");
		if(!isset($data['len'])) throw new SmartException("benefitDetail miss len");
		if($data['lv']<1) throw new SmartException("benefitDetail error lv");
		if($data['len']<1) throw new SmartException("benefitDetail error len");
		//获取订单
		$orderRecord=$r->orderRecord;
		if(!$orderRecord) throw new SmartException("addVip miss orderRecord");
		//获取会员
		$member=$orderRecord->member;
		if(!$member) throw new SmartException("addVip miss member");
		//找到该会员需要开通等级的最晚到期的记录
		$table=self::tableName();
		$sql="SELECT * FROM {$table} WHERE `memberId`='{$member->id}' AND `lv`='{$data['lv']}' ORDER BY `end` DESC;";
		$memberLvRecord=self::findBySql($sql)->one();
		//确定新纪录的开始时间
		$now=time();
		$start=NULL;
		if($memberLvRecord && $memberLvRecord->end>=$now) 
			$start=$memberLvRecord->end+1;
		else 
			$start=$now;
		if(!$start) throw new SmartException("miss start");
		//新增vip记录
		$memberLv=array();
		$memberLv['memberId']=$member->id;
		$memberLv['lv']=$data['lv'];
		$memberLv['start']=$start;
		$memberLv['end']=$memberLv['start']+$data['len'];
		$memberLv['orderId']=$orderRecord->id;
		self::addObj($memberLv);
	}
}