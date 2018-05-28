<?php
//会员
namespace backend\models\member;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
use backend\models\model\shop;
use backend\models\order\orderRecord;
use backend\models\product\formatPrice;
//========================================
class member extends source implements shop{
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initTime"));
	}
	//========================================
	//初始化用户创建时间
	public function initTime(){$this->createTime=time();}
	//========================================
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
		$vip=memberLv::getVipInfo($this);
		if(!$vip) return 0; else return $vip->lv;
	}
	//========================================
	//获取地址簿
	public function getAddressList(){
		return address::find()->where("`memberId`='{$this->id}' AND `isDeled`='0'")->all();
	}
	//========================================
	//获取会员信息的hash
	public function hash(){
		$data=$this->getData();
		$data['vip']=memberLv::getVipInfo($this);
		ksort($data);
		return md5(json_encode($data));
	}
	//========================================
	//上传头像
	public function uploadAvatar($avatar){
		if(!$avatar) throw new SmartException("miss avatar");
		//判断是否是可以被base64解码的字符串
		if($avatar!=base64_encode(base64_decode($avatar))) throw new SmartException("error b64");
		//解码
		$avatar=base64_decode($avatar); if(!$avatar) throw new SmartException("error avatar");
		//保存
		$this->updateObj(array('avatar'=>$avatar));
	}
	//========================================
	//上传昵称
	public function uploadNickName($nickName){
		if(!$nickName) throw new SmartException("miss nickName");
		//判断是否是可以被base64解码的字符串
		if($nickName!=base64_encode(base64_decode($nickName))) throw new SmartException("error b64");
		//解码
		$nickName=base64_decode($nickName); if(!$nickName) throw new SmartException("error nickName");
		//保存
		$this->updateObj(array('nickName'=>$nickName));
	}
	//========================================
	//获取节省金额
	public function getReduction(){
		//统计
		$table=orderRecord::tableName();
		$sql=
		"
		SELECT SUM(`reduction`) FROM {$table}
		WHERE 
			`memberId`='{$this->id}' 
			AND 
			`parentId` is NULL 
			AND 
			`payStatus`='1' 
			AND 
			`cancelStatus`='0' 
			AND 
			`closeStatus`='0'
		";
		$result=Yii::$app->db->createCommand($sql)->queryOne();
		//没有节省的情况
		if(!$result) return "0";
		if(!isset($result['SUM(`reduction`)'])) return "0";
		if(!$result['SUM(`reduction`)']) return "0";
		if($result['SUM(`reduction`)']=="0") return "0";
		//返回节省金额
		return formatPrice::formatPrice($result['SUM(`reduction`)']);
	}
}