<?php
//推荐记录
namespace backend\models\recommend;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
//========================================
class recommendRecord extends SmartActiveRecord{
	const RECOMMEND_TYPE_RECOMMEND=1;//推荐
	const RECOMMEND_TYPE_HOT=2;//热门
	//========================================
	//获取推荐的资源
	public function getSource(){return source::getRelationShip($this);}
}