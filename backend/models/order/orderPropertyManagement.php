<?php
//订单属性管理器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
//========================================
class orderPropertyManagement extends Component{
	//事件
	const EVENT_PROPERTY_CHANGED="EVENT_PROPERTY_CHANGED";//订单属性发生变化
	//========================================
	//订单记录
	public $orderRecord=NULL;
	//属性池(不能直接取,一定要通过getPropertyList()取)
	private $pList=false;
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_PROPERTY_CHANGED,array($this,"updatePropertyList"));
	}
	//========================================
	//添加订单属性
	public function addProperty($key,$val){
		//添加属性
		$orderProperty=array();
		$orderProperty['orderId']=$this->orderRecord->id;
		$orderProperty['propertyKey']=$key;
		$orderProperty['propertyVal']=$val;
		orderProperty::addObj($orderProperty);
		//触发事件,刷新属性池
		$this->trigger(self::EVENT_PROPERTY_CHANGED);
	}
	//========================================
	//获取属性
	public function getProperties(){
		$class=orderProperty::className();
		return $this->orderRecord->hasMany($class,array('orderId'=>'id'));
	}
	//========================================
	//更新属性池
	public function updatePropertyList(){
		//清空属性池
		$this->pList=array();
		//填充属性池
		foreach($this->getProperties()->all() as $p){
			//初始化propertyKey为索引的小池
			if(!isset($this->pList[$p->propertyKey])) $this->pList[$p->propertyKey]=array();
			//将属性值加入池中
			$data=array('val'=>$p->propertyVal,'time'=>$p->createTime,'obj'=>$p);
			$this->pList[$p->propertyKey][]=$data;
		}
	}
	//========================================
	//获取属性池
	public function getPropertyList(){
		if($this->pList===false) $this->updatePropertyList();
		return $this->pList;
	}
	//========================================
	//获取具体某个属性
	public function getProperty($key){
		//获取属性池
		$pList=$this->getPropertyList();
		//返回具体属性值
		if(!isset($pList[$key])) return NULL; else return $pList[$key];
	}
	//========================================
	//删除某个属性
	public function delProperty(orderProperty $p){
		//删除
		$p->delete();
		//触发事件,刷新属性池
		$this->trigger(self::EVENT_PROPERTY_CHANGED);
	}
}