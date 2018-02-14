<?php
//会员
namespace backend\models\member;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
//========================================
class member extends source{
	//返回资源类型
	public function getSourceType(){return source::TYPE_MEMBER;}
	//========================================
}