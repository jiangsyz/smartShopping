<?php
//标记
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class changeOrderPriceLog extends SmartActiveRecord{
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initCreateTime"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkMemo"));
	}
	//========================================
	//初始化时间
	public function initCreateTime(){$this->createTime=time();}
	//========================================
	//校验备注
	public function checkMemo(){if(!$this->memo) throw new SmartException("缺少备注",-2);}
}