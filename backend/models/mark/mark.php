<?php
//标记
namespace backend\models\mark;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\member\member;
use backend\models\model\source;
//========================================
class mark extends SmartActiveRecord{
	//标记类型
	const TYPE_COLLECTION=1;//收藏(针对产品)
	const TYPE_FOLLOW=2;//关注(针对商家)
	const TYPE_UPVOTE=3;//点赞(针对产品)
	//========================================
	//标记资源类型白名单
	const SOURCE_LIST=array(
		self::TYPE_COLLECTION=>array(source::TYPE_SPU),
		self::TYPE_FOLLOW=>array(source::TYPE_MEMBER),
		self::TYPE_COLLECTION=>array(source::TYPE_SPU),
	);
	//========================================
	//字段规则
	public function rules(){
	    return array(
	        //去空格
	        array(array(),'trim'),
	        //必填
	        array(array('memberId','markType','sourceType','sourceId'),'required'),
	        //唯一
	        array(array(),'unique'),
	    );
	}
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initTime"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkMember"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkSource"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkSourceType"));
	}
	//========================================
	//初始化标记创建时间
	public function initTime(){$this->createTime=time();}
	//========================================
	//获取标记者
	public function getMember(){return $this->hasOne(member::className(),array('id'=>'memberId'));}
	//========================================
	//获取推荐的资源
	public function getSource(){return source::getRelationShip($this);}
	//========================================
	//检查标记者
	public function checkMember(){if(!$this->member) throw new SmartException("miss member");}
	//========================================
	//检查标记资源
	public function checkSource(){if(!$this->source) throw new SmartException("miss source");}
	//========================================
	//检查标记资源类型
	public function checkSourceType(){
		//检查资源类型是否合法
		if(!in_array($this->sourceType,self::SOURCE_LIST[$this->markType]))
		throw new SmartException("error sourceType");
	}
}