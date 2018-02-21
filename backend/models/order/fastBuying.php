<?php
//快速购买行为
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\model\orderApplicant;
use backend\models\orderFactory\buyingRecord;
//========================================
class fastBuying extends Component{
	//会员
	public $member;
	//资源类型
	public $sourceType;
	//资源id
	public $sourceId;
	//购买数量
	public $buyCount;
	//购物行为
	private $buyingRecord;
	//========================================
	//初始化
	public function init(){
		parent::init();
		//初始化购物行为
		$buyingRecordConf=array();
		$buyingRecordConf['member']=$this->member;
		$buyingRecordConf['sourceType']=$this->sourceType;
		$buyingRecordConf['sourceId']=$this->sourceId;
		$buyingRecordConf['buyCount']=$this->buyCount;
		$buyingRecordConf['isSelected']=true;
		$this->buyingRecord=new buyingRecord($buyingRecordConf);
	}
	//========================================
	//获取申请方类型
	public function getOrderApplicantType(){return orderApplicant::TYPE_FAST_BUYING;}
	//========================================
	//获取结算单位下的所有购买行为
	public function getBuyingRecords(){return array($this->buyingRecord);}
	//========================================
	//获取会员
	public function getMember(){return $this->member;}
}