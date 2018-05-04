<?php
//退款
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
//========================================
class refund extends SmartActiveRecord{
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initData"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkPrice"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkPrice"));
	}
	//========================================
	//初始化数据
	public function initData(){
		$this->applyTime=time();
		$this->rejectHandlerType=NULL;
		$this->rejectHandlerId=NULL;
		$this->rejectTime=NULL;
		$this->rejectMemo=NULL;
		$this->refundHandlerType=NULL;
		$this->refundHandlerId=NULL;
		$this->refundTime=NULL;
		$this->refundMemo=NULL;
		$this->status=0;
	}
	//========================================
	//校验价格
	public function checkPrice(){if($this->price<=0) throw new SmartException("退款金额错误",2);}
}