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
	//========================================
	//返回资源类型
	public function getSourceType(){return source::TYPE_ORDER_RECORD;}
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initCreateTime"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initStatus"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initLocked"));
		$this->on(self::EVENT_AFTER_INSERT,array($this,"addAddress"));
		$this->on(self::EVENT_AFTER_INSERT,array($this,"addMemo"));
		$this->on(self::EVENT_AFTER_INSERT,array($this,"addDate"));
	}
	//========================================
	//初始化创建时间
	public function initCreateTime(){$this->createTime=time();}
	//========================================
	//初始化订单核心状态
	public function initStatus(){$this->status=0;}
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
	//添加订单属性
	public function addProperty($key,$val){
		$orderProperty=array();
		$orderProperty['orderId']=$this->id;
		$orderProperty['propertyKey']=$key;
		$orderProperty['propertyVal']=$val;
		orderProperty::addObj($orderProperty);
	}
	//========================================
	//添加收货地址
	public function addAddress(){
		//不需要收货地址的情况
		if(!$this->isNeedAddress) return;
		//确定命令中表明该订单记录收货地址的索引
		$addressIndex='address_'.$this->index;
		//客户端没有提交地址
		if(!isset($this->command[$addressIndex])) return;
		//获取地址
		$where="`id`='{$this->command[$addressIndex]}' AND `memberId`='{$this->memberId}' AND `isDeled`='0'";
		$address=address::find()->where($where)->one();
		if(!$address) throw new SmartException("miss address");
		//添加收货地址
		$this->addProperty('address',json_encode($address->getData()));
	}
	//========================================
	//添加备注
	public function addMemo(){
		//确定命令中表明该订单备注的索引
		$memoIndex='memo_'.$this->index;
		//客服端没有为该订单指定备注
		if(!isset($this->command[$memoIndex])) return;
		//添加会员备注
		$this->addProperty('memberMemo',$this->command[$memoIndex]);
	}
	//========================================
	//添加期望收获日期
	public function addDate(){
		//确定命令中表明该订单期望收获日期的索引
		$dateIndex='date_'.$this->index;
		//客服端没有为该订单指定备注
		if(!isset($this->command[$dateIndex])) return;
		//期望收货日期目前只支持1/2/4
		if(!in_array($this->command[$dateIndex],array(1,2,4))) throw new SmartException("error date");
		//添加期望收货日期
		$this->addProperty('date',$this->command[$dateIndex]);
	}
}