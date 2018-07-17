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
		self::log(static::tableName(),'NULL',json_encode($model->attributes));
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
			self::log(static::tableName(),json_encode($this->oldAttributes),json_encode($this->attributes));
			//修改
			$result=$this->update();
			if($result!=1) throw new SmartException(json_encode($this->getErrors())."({$result})");
		}
		return true;
	}
	//========================================
	private static function log($modelName,$originaData,$data){
		//组织数据
		$time=time();
		$runningId=Yii::$app->controller->runningId;
		$sql=
		"
		INSERT INTO `model_db_log` (`id`,`runningId`,`modelName`,`originaData`,`data`,`time`) 
		VALUES(NULL,'{$runningId}','{$modelName}','{$originaData}','{$data}','{$time}');
		";
		//根据数据改动的表所在库决定执行sql的库
		$db=static::getDb();
		//执行
		$db->createCommand($sql)->execute();
	}
}