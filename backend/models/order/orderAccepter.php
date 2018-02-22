<?php
//订单受理单元
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use yii\base\Event;
use backend\models\model\orderApplicant;
//========================================
class orderAccepter extends Component{
	//事件
	const EVENT_CREATE_ORDER_SUCCESS='EVENT_CREATE_ORDER_SUCCESS';//订单创建成果
	//========================================
	//订单申请单元
	public $orderApplicant=NULL;
	//订单工厂
	public $mainOrderFactory=NULL;
	//主订单
	public $mainOrder=false;
	//主订单记录
	public $mainOrderRecord=false;
	//========================================
	//初始化
	public function init(){
		parent::init();
		//将订单申请者的购买行为添加到主订单工程
		foreach($this->orderApplicant->getBuyingRecords() as $r) 
			$this->mainOrderFactory->addBuyingRecord($r);
		//初始化订单
		$this->mainOrder=$this->mainOrderFactory->initOrder($this->orderApplicant->getMember());
	}
	
}