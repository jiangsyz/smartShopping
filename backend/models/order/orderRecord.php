<?php
//订单记录
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
use backend\models\member\address;
use backend\models\member\member;
//========================================
class orderRecord extends source{
	//创建订单时所需的外部命令
	public $command=NULL;
	//属性管理器
	public $propertyManagement=false;
	//地址管理器
	public $addressManagement=false;
	//备注管理器
	public $memoManagement=false;
	//预期收货日期管理器
	public $dateManagement=false;
	//订单关系管理器
	public $relationManagement=false;
	//购买行为管理器
	public $buyingManagement=false;
	//支付管理器
	public $payManagement=false;
	//状态管理器
	public $statusManagement=false;
	//取消管理器
	public $cancelManagement=false;
	//检查器
	public $checker=false;
	//数据提取器
	public $extraction=false;
	//========================================
	//返回资源类型
	public function getSourceType(){return source::TYPE_ORDER_RECORD;}
	//========================================
	//返回会员
	public function getMember(){return $this->hasOne(member::className(),array('id'=>'memberId'));}
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->propertyManagement=new orderPropertyManagement(array('orderRecord'=>$this));
		$this->addressManagement=new orderAddressManagement(array('orderRecord'=>$this));
		$this->memoManagement=new orderMemoManagement(array('orderRecord'=>$this));
		$this->dateManagement=new orderDateManagement(array('orderRecord'=>$this));
		$this->relationManagement=new orderRelationshipManagement(array('orderRecord'=>$this));
		$this->buyingManagement=new orderBuyingManagement(array('orderRecord'=>$this));
		$this->payManagement=new orderPayManagement(array('orderRecord'=>$this));
		$this->statusManagement=new orderStatusManagement(array('orderRecord'=>$this));
		$this->cancelManagement=new orderCancelManagement(array('orderRecord'=>$this));
		$this->checker=new orderRecordChecker(array('orderRecord'=>$this));
		$this->extraction=new orderExtraction(array('orderRecord'=>$this));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initCreateTime"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initStatus"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initLocked"));
		$this->on(self::EVENT_AFTER_INSERT,array($this->addressManagement,"addAddress"));
		$this->on(self::EVENT_AFTER_INSERT,array($this->memoManagement,"addMemberMemo"));
		$this->on(self::EVENT_AFTER_INSERT,array($this->dateManagement,"addDate"));
	}
	//========================================
	//初始化创建时间
	public function initCreateTime(){$this->createTime=time();}
	//========================================
	//初始化各类状态
	public function initStatus(){
		$this->payStatus=0;
		$this->cancelStatus=0;
		$this->closeStatus=0;
		$this->deliverStatus=0;
		$this->refundingStatus=0;
		$this->finishStatus=0;
	}
	//========================================
	//初始化订单锁定状态
	public function initLocked(){$this->locked=0;}
	//========================================
	//获取通过订单id获取一个锁住的订单
	public static function getLockedOrderById($id){
		$table=self::tableName();
		$sql="SELECT * FROM {$table} WHERE `id`='{$id}' FOR UPDATE";
		return self::findBySql($sql)->one();
	}
}