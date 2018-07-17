<?php
//会员
namespace backend\models\member;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
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
		$vipData=memberLv::getVipData($this);
		return $vipData['lv'];
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
		$data['vip']=memberLv::getVipData($this);
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
	//========================================
	//同步有赞的vip数据
	public function syncYouzanVip(){
		//同步过的不处理
		$syncYouzanVip=$this->getProperty("syncYouzanVip"); if($syncYouzanVip) return;
		//同步有赞vip,必须要用户当前没有任何有效的vip
		$memberLvs=memberLv::find()->where("`memberId`='{$this->id}' AND `closed`='0'")->all();
		if($memberLvs) 
			return $this->addProperty("syncYouzanVip","memberLvs is not empty");
		//获取unionid
		$unionid=$this->getProperty("unionid");
		if(!$unionid) 
			return $this->addProperty("syncYouzanVip","member miss unionid");
		//获取在公众号中的openid
		$publicAccountUser=publicAccountUser::find()->where("`unionid`='{$unionid}'")->one();
		if(!$publicAccountUser) 
			return $this->addProperty("syncYouzanVip","member is not publicAccountUser");
		//通过openid去card里查
		$tableName=youzanCard::tableName();
		$sql="SELECT * FROM {$tableName} WHERE `yz_openid`='{$publicAccountUser->openid}' FOR UPDATE";
		$cards=youzanCard::findBySql($sql)->all();
		//循环插入
		$success=array();
		foreach($cards as $card){
			//同步过的卡不管
			if($card->result) continue;
			//新增vip记录
			$memberLvData=array();
			$memberLvData['memberId']=$this->id;
			$memberLvData['lv']=1;
			$memberLvData['start']=$card->start_time;
			$memberLvData['end']=$card->end_time;
			$memberLvData['handlerType']=999;
			$memberLvData['handlerId']=$card->card_no;
			$memberLv=memberLv::addObj($memberLvData);
			//记录成功同步的卡号
			$success[]=$card->card_no;
			//反向记录同步结果
			$card->updateObj(array('result'=>$memberLv->id));
		}
		//标记为同步结果,记录同步数量
		$successCount=count($success);
		return $this->addProperty("syncYouzanVip","ok({$successCount})");
	}
}