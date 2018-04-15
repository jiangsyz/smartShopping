<?php
//购物车记录
namespace backend\models\shoppingCart;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\member\member;
use backend\models\model\source;
//========================================
class shoppingCartRecord extends SmartActiveRecord{
	//字段规则
	public function rules(){
	    return array(
	        //去空格
	        array(array(),'trim'),
	        //必填
	        array(array('memberId','sourceType','sourceId','count'),'required'),
	        //唯一
	        array(array(),'unique'),
	    );
	}
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkSalesUnit"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkMember"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkKeepCount"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkSalesUnit"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkMember"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkKeepCount"));
	}
	//========================================
	//根据售卖单元类型获取不同售卖单元
	public function getSalesUnit(){return source::getRelationShip($this);}
	//========================================
	//获取会员
	public function getMember(){return self::hasOne(member::className(),array('id'=>'memberId'));}
	//========================================
	//获取总价
	public function getTotalPrice(){return $this->salesUnit->getPrice($this->member)*$this->count;}
	//========================================
	//获取成交总价
	public function getTotalFinalPrice(){
		return $this->salesUnit->getFinalPrice($this->member)*$this->count;
	}
	//========================================
	//获取基础数据
	public function getData($keys=array()){
		$data=parent::getData($keys);
		$data['isSelected']=$this->isSelected();
		return $data;
	}
	//========================================
	//获取是否被选中(这是为以后改进留出接口)
	public function isSelected(){return true;}
	//========================================
	//检查会员是否存在
	public function checkMember(){if(!$this->member) throw new SmartException("miss member");}
	//========================================
	//检查售卖对象
	public function checkSalesUnit(){
		//是否存在
		if(!$this->salesUnit) 
			throw new SmartException("miss salesUnit");
		//是否允许销售
		if(!$this->salesUnit->isAllowSale()) 
			throw new SmartException("销售单元({$this->salesUnit->getSourceNo()})不允许销售",-2);
	}
	//========================================
	//检查购买数量是否合法
	public function checkKeepCount(){
		if($this->count<1) throw new SmartException("count<1");
		//获取售卖单元库存
		$salesUnitCount=$this->salesUnit->getKeepCount();
		//加入购物车数量不得超过售卖单元库存($salesUnitCount=NULL意味着没有库存限制)
		if($salesUnitCount===NULL) return;
		if($this->count>$salesUnitCount)
			throw new SmartException("销售单元({$this->salesUnit->getSourceNo()})库存不足",-2);	
	}
}