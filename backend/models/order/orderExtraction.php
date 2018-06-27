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
	//获取订单的美化后的id
	public function getShowId(){return $this->orderRecord->code;}
	//========================================
	//获取基础数据
	public function getBasicData(){
		$data=array();
		//订单编号
		$data['id']=$this->orderRecord->id;
		//对外美化的订单编号
		$data['showId']=$this->getShowId();
		//支付价格
		$data['pay']=formatPrice::formatPrice($this->orderRecord->pay/100);
		//商品价格
		$data['finalPrice']=formatPrice::formatPrice($this->orderRecord->finalPrice);
		//运费
		$data['freight']=formatPrice::formatPrice($this->orderRecord->freight);
		//状态
		$data['status']=$this->orderRecord->statusManagement->getStatus();
		//物流状态
		$data['deliverStatus']=$this->orderRecord->deliverStatus;
		//是否需要地址
		$data['isNeedAddress']=$this->orderRecord->isNeedAddress;
		//获取购买行为
		$data['buyingRecords']=array();
		$buyingRecords=$this->orderRecord->buyingManagement->getBuyingList();
		foreach($buyingRecords as $b){
			$bInfo=json_decode($b->dataPhoto,true);
			$bInfo['buyingCount']=$b->buyingCount;
			$data['buyingRecords'][]=$bInfo;
		}
		//返回数据
		return $data;
	}
	//========================================
	//获取订单详情
	public function getDetail(){
		$data=array();
		//订单编号
		$data['id']=$this->orderRecord->id;
		//对外美化的订单编号
		$data['showId']=$this->getShowId();
		//支付价格
		$data['pay']=formatPrice::formatPrice($this->orderRecord->pay/100);
		//商品价格
		$data['finalPrice']=formatPrice::formatPrice($this->orderRecord->finalPrice);
		//运费
		$data['freight']=formatPrice::formatPrice($this->orderRecord->freight);
		//状态
		$data['status']=$this->orderRecord->statusManagement->getStatus();
		//物流状态
		$data['deliverStatus']=$this->orderRecord->deliverStatus;
		//支付剩余时间
		$data['payRemainingTime']=$this->orderRecord->payManagement->getPayRemainingTime();
		//是否需要地址
		$data['isNeedAddress']=$this->orderRecord->isNeedAddress;
		//地址信息
		$data['address']=$this->orderRecord->addressManagement->getAddress();
		//备注信息
		$data['memo']=$this->orderRecord->memoManagement->getMemo(1);
		//创建时间
		$data['createTime']=$this->orderRecord->createTime;
		//获取购买行为
		$data['buyingRecords']=array();
		$childOrders=$this->orderRecord->relationManagement->getChildren();
		foreach($childOrders as $c){
			$bData=array();
			$bData['title']=$c->title;
			$bData['buyingRecords']=array();
			$buyingRecords=$c->buyingManagement->getBuyingRecords();
			foreach($buyingRecords as $b){
				$bInfo=$b->getSalesUnit()->getExtraction()->getBasicData($this->orderRecord->member);
				$bInfo['buyingCount']=$b->buyingCount;
				$bData['buyingRecords'][]=$bInfo;
			}
			$data['buyingRecords'][]=$bData;
		}
		//返回数据
		return $data;
	}
}