<?php
//会员等级
namespace backend\models\member;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\order\orderBuyingRecord;
use backend\models\notice\notice;
//========================================
class memberLv extends SmartActiveRecord{
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initClosedData"));
	}
	//========================================
	//初始化会员等级关闭相关数据
	public function initClosedData(){
		$this->closed=0;
		$this->closedMemo=NULL;
	}
	//========================================
	//获取同等级且连续且有效的下一个vip
	public function getNextVip(){
		//自身必须有效
		if($this->closed!=0) throw new SmartException("memberLv closed <> 0");
		//下一个连续vip的开始时间
		$start=$this->end+1;
		//查找下一个连续的有效vip
		$where="`memberId`='{$this->memberId}' AND `lv`='{$this->lv}' AND `start`='{$start}' AND `closed`='0'";
		$vips=self::find()->where($where)->all();
		//统计找到的个数
		$count=count($vips);
		//找不到正常,返回null
		if($count==0) return NULL;
		//找到一个正常,返回找到的
		elseif($count==1) return $vips[0];
		//找到不止一个就凉凉了
		else throw new SmartException("next vip count {$count}");
	}
	//========================================
	//获取同等级且连续且有效的上一个vip
	public function getPreviousVip(){
		//自身必须有效
		if($this->closed!=0) throw new SmartException("memberLv closed <> 0");
		//上一个连续vip的结束时间
		$end=$this->start-1;
		//查找上一个连续的有效vip
		$where="`memberId`='{$this->memberId}' AND `lv`='{$this->lv}' AND `end`='{$end}' AND `closed`='0'";
		$vips=self::find()->where($where)->all();
		//统计找到的个数
		$count=count($vips);
		//找不到正常,返回null
		if($count==0) return NULL;
		//找到一个正常,返回找到的
		elseif($count==1) return $vips[0];
		//找到不止一个就凉凉了
		else throw new SmartException("previous vip count {$count}");
	}
	//========================================
	//获取同等级且连续且有效的后续vip列表,形如(下一个,再下一个,...)
	public function getNextVips(){
		//自身必须有效
		if($this->closed!=0) throw new SmartException("memberLv closed <> 0");
		//后续vip池
		$nextVips=array();
		//检查对象
		$check=$this;
		//递归获取连续且有效的下一个vip
		while($check){
			$check=$check->getNextVip(); if($check) array_push($nextVips,$check);
		}
		return $nextVips;
	}
	//========================================
	//获取同等级且连续且有效的前置vip列表,形如(...,再上一个,上一个)
	public function getPreviousVips(){
		//自身必须有效
		if($this->closed!=0) throw new SmartException("memberLv closed <> 0");
		//前置vip池
		$previousVips=array();
		//检查对象
		$check=$this;
		//递归获取连续且有效的上一个vip
		while($check){
			$check=$check->getPreviousVip(); if($check) array_unshift($previousVips,$check);
		}
		return $previousVips;
	}
	//========================================
	//获取某个等级的当前命中vip
	public static function getCurrentVipByLv(member $m,$lv){
		//查找当前能命中的vip
		$now=time();
		$where="`lv`='{$lv}' AND `start`<='{$now}' AND `end`>='{$now}' AND `memberId`='{$m->id}' AND `closed`='0'";
		$vips=self::find()->where($where)->all();
		//统计找到的个数
		$count=count($vips);
		//找不到正常,返回null
		if($count==0) return NULL;
		//找到一个正常,返回找到的
		elseif($count==1) return $vips[0];
		//找到不止一个就凉凉了
		else throw new SmartException("current vip count {$count}");
	}
	//========================================
	//获取最高等级的当前命中vip
	public static function getCurrentVip(member $m){
		$currentVip=NULL;
		//查找当前能命中的vip
		$now=time();
		$where="`start`<='{$now}' AND `end`>='{$now}' AND `memberId`='{$m->id}' AND `closed`='0'";
		$vips=self::find()->where($where)->all();
		//循环找出等级最高的vip
		foreach($vips as $vip){
			if($currentVip===NULL) $currentVip=$vip;
			//同一等级不可能命中两次,因为我们的设计规则是同一等级的有效vip不能在时间上产生重合
			elseif($currentVip->lv==$vip->lv) throw new SmartException("您的vip会员日期有重合,请联系客服",-2);
			//高等级替代低等级
			elseif($currentVip->lv<$vip->lv) $currentVip=$vip;
		}
		return $currentVip;
	}
	//========================================
	//获取会员的vip数据
	public static function getVipData(member $m){
		//默认假设为非会员
		$data=array('lv'=>0,'start'=>0,'end'=>0);
		//获取最高等级的当前命中vip,找不到就是非会员
		$currentVip=self::getCurrentVip($m); if(!$currentVip) return $data;
		//找到就假设当前命中的为最终结果
		$data=array('lv'=>$currentVip->lv,'start'=>$currentVip->start,'end'=>$currentVip->end);
		//取前置,修正start
		$previousVips=$currentVip->getPreviousVips();
		if($previousVips) $data['start']=$previousVips[0]->start;
		//取后续,修正end
		$nextVips=$currentVip->getNextVips();
		if($nextVips) $data['end']=$nextVips[count($nextVips)-1]->end;
		//返回最终计算结果
		return $data;
	}
	//========================================
	//开通vip
	public static function addVipBybuyingRecord(orderBuyingRecord $r,$data){
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
		//找到该会员需要开通等级的未关闭最晚到期的记录
		$table=self::tableName();
		$sql="SELECT * FROM {$table} WHERE `memberId`='{$member->id}' AND `lv`='{$data['lv']}' AND `closed`='0' ORDER BY `end` DESC FOR UPDATE;";
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
		$memberLv['handlerType']=$r->getSourceType();
		$memberLv['handlerId']=$r->getSourceId();
		self::addObj($memberLv);
		//格式化到期时间
		$endDate=date("Y-m-d",$memberLv['end']);
		//发送通知
        $notice=array();
        $notice['memberId']=$member->id;
        $notice['type']=notice::TYPE_VIP;
        $notice['content']="您的超级会员身份最新截至日期为{$endDate}!";
        notice::addObj($notice);
	}
}