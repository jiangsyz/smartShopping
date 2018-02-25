<?php
//购物车
namespace backend\models\shoppingCart;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\member\member;
use backend\models\model\orderApplicant;
use backend\models\orderFactory\buyingRecord;
use backend\models\order\orderBackground;
//========================================
class shoppingCart extends Component implements orderApplicant{
	//会员
	public $member=NULL;
	//购物车记录集合
	private $shoppingCartRecords=array();
	//========================================
	//初始化
	public function init(){
		parent::init();
		//获取用户购物车记录
		$this->shoppingCartRecords=shoppingCartRecord::find()->with('salesUnit')->where("`memberId`='{$this->member->id}'")->all();
	}
	//========================================
	//获取购物车记录集合
	public function getShoppingCartRecords(){return $this->shoppingCartRecords;}
	//========================================
	//获取申请方类型
	public function getOrderApplicantType(){return orderApplicant::TYPE_SHOPPING_CART;}
	//========================================
	//获取申请单元下的所有购买行为
	public function getBuyingRecords(){
		$buyingRecords=array();
		//添加购买行为
		foreach($this->getShoppingCartRecords() as $shoppingCartRecord){
			$buyingRecordData=array();
			$buyingRecordData['member']=$this->member;
			$buyingRecordData['sourceType']=$shoppingCartRecord->sourceType;
			$buyingRecordData['sourceId']=$shoppingCartRecord->sourceId;
			$buyingRecordData['buyCount']=$shoppingCartRecord->count;
			$buyingRecordData['isSelected']=$shoppingCartRecord->isSelected();
			$buyingRecord=new buyingRecord($buyingRecordData);
			$buyingRecords[]=$buyingRecord;
		}
		return $buyingRecords;
	}
	//========================================
	//获取会员
	public function getMember(){return $this->member;}
}