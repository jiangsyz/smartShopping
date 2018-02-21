<?php
//某个会员的单个购买行为(即某个一资源买多少件)
namespace backend\models\orderFactory;
use Yii;
use yii\base\SmartException;
use yii\base\Component;
use backend\models\model\source;
use backend\models\model\salesUnit;
use backend\models\member\member;
//========================================
class buyingRecord extends Component{
	//会员
	public $member;
	//资源类型
	public $sourceType;
	//资源id
	public $sourceId;
	//售卖数量
	public $buyCount;
	//是否被选中
	public $isSelected;
	//购买目标
	private $salesUnit;
	//========================================
	//初始化
	public function init(){
		parent::init();
		//购买数量必须>0
		if($this->buyCount<=0) throw new SmartException("error buyCount");
		//获取购买目标
		$this->salesUnit=source::getSource($this->sourceType,$this->sourceId);
		if(!$this->salesUnit) throw new SmartException("miss salesUnit");
		//购买目标必须为售卖单元
		$class=salesUnit::className();
		if(!($this->salesUnit instanceof $class)) throw new SmartException("source is not salesUnit");
	}
	//========================================
	//获取原价
	public function getPrice(){return $this->salesUnit->getPrice($this->member)*$this->buyCount;}	
	//========================================
	//获取成交价格
	public function getFinalPrice(){return $this->salesUnit->getFinalPrice($this->member)*$this->buyCount;}
}