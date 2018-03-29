<?php
//会员地址
namespace backend\models\member;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
//========================================
class address extends SmartActiveRecord{
	//字段规则
	public function rules(){
	    return array(
	        //去空格
	        array(array('name','phone','address','postCode'),'trim'),
	        //必填
	        array(array('memberId','name','phone','areaId','address'),'required'),
	        //唯一
	        array(array(),'unique'),
	    );
	}
	//========================================
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initCreateTime"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initIsDeled"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkArea"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkMember"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkArea"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkMember"));
		$this->on(self::EVENT_BEFORE_UPDATE,array($this,"checkIsDeled"));
	}
	//========================================
	//初始化时间
	public function initCreateTime(){$this->createTime=time();}
	//========================================
	//初始化软删除状态
	public function initIsDeled(){$this->isDeled=0;}
	//========================================
	//获取数据
	public function getData($keys=array()){
		//获取收获地址基础数据
		$data=parent::getData($keys);
		//冗余完整区域信息		
		$data['fullAreaName']=$this->area->full_area_name;
		//返回数据
		return $data;
	}
	//========================================
	//获取会员
	public function getMember(){return $this->hasOne(member::className(),array('id'=>'memberId'));}
	//========================================
	//获取区域
	public function getArea(){
		return $this->hasOne(Yii::$app->smartArea->smartAreaRecord,array('area_id'=>'areaId'));
	}
	//========================================
	//检查区域
	public function checkArea(){
		//区域必须存在
		if(!$this->area) throw new SmartException("miss area");
		//必须是第三级区域
		if($this->area->level!=3) throw new SmartException("error area level");
	}
	//========================================
	//检查会员
	public function checkMember(){if(!$this->member) throw new SmartException("miss member");}
	//========================================
	//检查软删除状态
	public function checkIsDeled(){
		if($this->oldAttributes['isDeled']) throw new SmartException("address is deled");
	}
	//========================================
	//重载delete进行软删除
	public function delete(){$this->updateObj(array('isDeled'=>1));}
}