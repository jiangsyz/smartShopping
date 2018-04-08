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
	public function getShowId(){return 900000+$this->orderRecord->id;}
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
		//订单标题
		$data['title']=$this->orderRecord->title;
		//父订单id
		$data['parentId']=$this->orderRecord->parentId;
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
		//地址信息
		$data['address']=$this->orderRecord->addressManagement->getAddress();
		//备注信息
		$data['memo']=$this->orderRecord->memoManagement->getMemo(1);
		//获取购买行为
		$data['buyingRecords']=array();
		$childOrders=$this->orderRecord->relationManagement->getChildren();
		foreach($childOrders as $c){
			$key=$c->title;
			if(isset($data['buyingRecords'][$key])) throw new SmartException("title existed");
			$data['buyingRecords'][$key]=array();
			$buyingRecords=$c->buyingManagement->getBuyingRecords();
			foreach($buyingRecords as $b){
				$bInfo=$b->getSalesUnit()->getExtraction()->getBasicData($this->orderRecord->member);
				$bInfo['buyingCount']=$b->buyingCount;
				$data['buyingRecords'][$key][]=$bInfo;
			}
		}
		//返回数据
		return $data;
	}
}