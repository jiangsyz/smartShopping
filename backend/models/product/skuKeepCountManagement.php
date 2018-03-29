<?php
//sku库存管理器
namespace backend\models\product;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\model\source;
//========================================
class skuKeepCountManagement extends Component{
	public $sku=NULL;
	public $handlerType=NULL;
	public $handlerId=NULL;
	public $keepCount=NULL;
	public $memo=NULL;
	public $handler=false;
	public $originaKeepCount=false;
	//========================================
	//初始化
	public function init(){
		parent::init();
		//获取原价
		$this->originaKeepCount=$this->sku->count;
		//获取操作者
		$this->handler=source::getSource($this->handlerType,$this->handlerId);
		if(!$this->handler) throw new SmartException("error handler");
		//操作者被锁定不能修改
		if($this->handler->isLocked()) throw new SmartException("handler isLocked");
		//检查修改后的库存数
		if($this->keepCount<0) throw new SmartException("error keepCount");
		//检查是否修改
		if($this->keepCount==$this->originaKeepCount) throw new SmartException("keepCount==origina");
		//修改
		$this->sku->updateObj(array('count'=>$this->keepCount));
		//记录日志
		salesUnitKeepCountLog::addObj($this->getLogData());
	}
	//========================================
	//获取记录日志所需的数据
	private function getLogData(){
		$logData=array();
		$logData['sourceType']=$this->sku->getSourceType();
		$logData['sourceId']=$this->sku->getSourceId();
		$logData['handlerType']=$this->handlerType;
		$logData['handlerId']=$this->handlerId;
		$logData['keepCount']=$this->keepCount;
		$logData['originaKeepCount']=$this->originaKeepCount;
		$logData['memo']=$this->memo;
		return $logData;
	}
}