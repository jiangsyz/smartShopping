<?php
//员工
namespace backend\models\staff;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
use backend\models\model\source;
use backend\models\identifyingCode\identifyingCodeManagement;
use backend\models\token\tokenManagement;
//========================================
class staff extends source{
	//返回资源类型
	public function getSourceType(){return source::TYPE_STAFF;}
}