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
	public $handler=false;
	public $originaKeepCount=false;
	//========================================
	//初始化
	public function init(){
		parent::init();
		//获取操作者
		$this->handler=source::getSource($this->handlerType,$this->handlerId);
		//获取原价
		$this->originaKeepCount=$this->sku->count;
		if(!$this->handler) throw new SmartException("error handler");
		//操作者被锁定不能修改
		if($this->handler->isLocked()) throw new SmartException("handler isLocked");
		//检查修改后的库存数
		if($this->keepCount<0) throw new SmartException("error keepCount");
		//检查是否修改
		if($this->keepCount==$this->originaKeepCount) 
			throw new SmartException("keepCount==originaKeepCount");
		//修改
		$this->sku->updateObj(array('count'=>$this->keepCount));
	}
}