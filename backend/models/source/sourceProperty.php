<?php
//资源属性
namespace backend\models\source;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
//========================================
class sourceProperty extends LogActiveRecord{
	//字段规则
	public function rules(){
		return array(
			//字符串
			[['propertyKey','propertyVal'],'string','max'=>200],
		);
	}
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initCreateTime"));
	}
	//========================================
	//初始化创建时间
	public function initCreateTime(){$this->createTime=time();}
}