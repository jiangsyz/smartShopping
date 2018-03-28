<?php
//订单记录
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
use backend\models\member\address;
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
	//检查器
	public $checker=false;
	//========================================
	//返回资源类型
	public function getSourceType(){return source::TYPE_ORDER_RECORD;}
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->propertyManagement=new orderPropertyManagement(array('orderRecord'=>$this));
		$this->addressManagement=new orderAddressManagement(array('orderRecord'=>$this));
		$this->memoManagement=new orderMemoManagement(array('orderRecord'=>$this));
		$this->dateManagement=new orderDateManagement(array('orderRecord'=>$this));
		$this->checker=new orderRecordChecker(array('orderRecord'=>$this));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initCreateTime"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initPayStatus"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initCancelStatus"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initLocked"));
		$this->on(self::EVENT_AFTER_INSERT,array($this->addressManagement,"addAddress"));
		$this->on(self::EVENT_AFTER_INSERT,array($this->memoManagement,"addMemberMemo"));
		$this->on(self::EVENT_AFTER_INSERT,array($this->dateManagement,"addDate"));
	}
	//========================================
	//初始化创建时间
	public function initCreateTime(){$this->createTime=time();}
	//========================================
	//初始化订单支付状态
	public function initPayStatus(){$this->payStatus=0;}
	//========================================
	//初始化订单取消状态
	public function initCancelStatus(){$this->cancelStatus=0;}
	//========================================
	//初始化订单锁定状态
	public function initLocked(){$this->locked=0;}
	//========================================
	//获取父订单(加锁)
	public function getParentOrder(){
		$table=self::tableName();
		$sql="SELECT * FROM {$table} WHERE `id`='{$this->parentId}' FOR UPDATE";
		return self::findBySql($sql)->one();
	}
	//========================================
	//获取子订单(加锁)
	public function getChildOrders(){
		$table=self::tableName();
		$sql="SELECT * FROM {$table} WHERE `parentId`='{$this->id}' FOR UPDATE";
		return self::findBySql($sql)->all();
	}
	//========================================
	//获取购买行为(加锁)
	public function getBuyingRecords(){
		$table=orderBuyingRecord::tableName();
		$sql="SELECT * FROM {$table} WHERE `orderId`='{$this->id}' FOR UPDATE";
		return orderBuyingRecord::findBySql($sql)->all();	
	}	
}