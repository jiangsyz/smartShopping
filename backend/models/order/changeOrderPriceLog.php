<?php
//标记
namespace backend\models\order;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
//========================================
class changeOrderPriceLog extends LogActiveRecord{
	//字段规则
	public function rules(){
        return array(
        	//必填
        	[['handlerType','handlerId','orderId','originaData','data','memo'],'required'],
        	//整型
            [['handlerType','handlerId','orderId','createTime'],'integer'],
            //字符串
            [['originaData','data','memo'],'string','max'=>300],
        );
    }
	//========================================	
	//初始化
	public function init(){
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"initCreateTime"));
		$this->on(self::EVENT_BEFORE_INSERT,array($this,"checkMemo"));
	}
	//========================================
	//初始化时间
	public function initCreateTime(){$this->createTime=time();}
	//========================================
	//校验备注
	public function checkMemo(){if(!$this->memo) throw new SmartException("缺少备注",-2);}
}