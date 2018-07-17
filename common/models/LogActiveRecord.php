<?php
//行为追踪
namespace common\models;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class LogActiveRecord extends SmartActiveRecord{
	static function addObj($param=array()){
		if(!is_array($param)) throw new SmartException("param is not array");
		$model=new static($param);
		if(!$model->insert()) throw new SmartException(json_encode($model->getErrors()));
		//记录日志
		$modelDbLog=array();
		$modelDbLog['modelName']=static::tableName();
		$modelDbLog['originaData']='NULL';
		$modelDbLog['data']=json_encode($model->attributes);
		modelDbLog::addObj($modelDbLog);
		return $model;
	}
	//========================================
	public function updateObj($param=array()){
		//被修改字段的数量
		$changeCount=0;
		if(!is_array($param)) throw new SmartException("param is not array");
		foreach($param as $key=>$val){
			//字段被修改了
			if($this->$key!=$val){
				$this->$key=$val;
				$changeCount++;
			}
		}
		//修改
		if($changeCount>0){
			//记录日志
			$modelDbLog=array();
			$modelDbLog['modelName']=static::tableName();
			$modelDbLog['originaData']=json_encode($this->oldAttributes);
			$modelDbLog['data']=json_encode($this->attributes);
			modelDbLog::addObj($modelDbLog);
			$result=$this->update();
			if($result!=1) throw new SmartException(json_encode($this->getErrors())."({$result})");
		}
		return true;
	}
}