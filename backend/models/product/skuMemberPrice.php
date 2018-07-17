<?php
//库存计量单元的会员价
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
use backend\models\model\source;
//========================================
class skuMemberPrice extends LogActiveRecord{
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkLevel"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkPrice"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkSku"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkLevel"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkPrice"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkSku"));
	}
	//========================================
	public function getSku(){return $this->hasOne(sku::className(),array('id'=>'skuId'));}
	//========================================
	public function checkSku(){if(!$this->sku) throw new SmartException("miss sku");}
	//========================================
	public function checkLevel(){if($this->lv<0) throw new SmartException("error lv");}
	//========================================
	public function checkPrice(){if($this->price<0) throw new SmartException("error price");}
}