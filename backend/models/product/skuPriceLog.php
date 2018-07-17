<?php
//sku价格日志
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
//========================================
class skuPriceLog extends LogActiveRecord{
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initCreateTime"));
	}
	//========================================
	//初始化创建时间
	public function initCreateTime(){$this->createTime=time();}
}