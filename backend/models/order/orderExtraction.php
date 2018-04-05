<?php
//订单数据提取器
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\product\formatPrice;
//========================================
class orderExtraction extends Component{
	//订单记录
	public $orderRecord=NULL;
	//========================================
	//获取基础数据
	public function getBasicData(){
		$data=array();
		//订单编号
		$data['id']=$this->orderRecord->id;
		//对外美化的订单编号
		$data['showId']=900000+$this->orderRecord->id;
		//支付价格
		$data['pay']=formatPrice::formatPrice($this->orderRecord->pay/100);
		//商品价格
		$data['finalPrice']=formatPrice::formatPrice($this->orderRecord->finalPrice);
		//运费
		$data['freight']=formatPrice::formatPrice($this->orderRecord->freight);
		//状态
		$data['status']=$this->orderRecord->statusManagement->getStatus();
		//是否需要地址
		$data['isNeedAddress']=$this->orderRecord->isNeedAddress;
		//获取购买行为
		$data['buyingRecords']=array();
		$buyingRecords=$this->orderRecord->buyingManagement->getBuyingList();
		foreach($buyingRecords as $b){
			$bInfo=$b->getSalesUnit()->getExtraction()->getBasicData($this->orderRecord->member);
			$bInfo['buyingCount']=$b->buyingCount;
			$data['buyingRecords'][]=$bInfo;
		}
		return $data;
	}
}