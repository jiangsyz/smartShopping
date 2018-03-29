<?php
//订单关系管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderRelationshipManagement extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//获取父订单
	public function getParent($lockFlag=false){
		if($lockFlag){
			$table=orderRecord::tableName();
			$sql="SELECT * FROM {$table} WHERE `id`='{$this->orderRecord->parentId}' FOR UPDATE";
			return orderRecord::findBySql($sql)->one();
		}
		else{
			return orderRecord::find()->where("`id`='{$this->orderRecord->parentId}'")->one();	
		}
	}
	//========================================
	//获取子订单
	public function getChildren($lockFlag=false){
		if($lockFlag){
			$table=orderRecord::tableName();
			$sql="SELECT * FROM {$table} WHERE `parentId`='{$this->orderRecord->id}' FOR UPDATE";
			return orderRecord::findBySql($sql)->all();
		}
		else{
			return orderRecord::find()->where("`parentId`='{$this->orderRecord->id}'")->all();	
		}
	}
	//========================================
	//获取后代订单
	public function getPosterities($lockFlag=false){
		//用栈代替递归,寻找直接子订单和间接子订单
		$checkList=$posterities=$this->getChildren($lockFlag);
		while(!empty($checkList)){
			$ckeck=array_shift($checkList);
			$children=$ckeck->relationManagement->getChildren($lockFlag);
			foreach($children as $c) $checkList[]=$posterities[]=$c;
		}
		//返回后代订单
		return $posterities;
	}
}