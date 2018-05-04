<?php
use yii\db\Migration;
class m180503_032835_change_column_to_refund_table extends Migration{
    public function up(){
        $this->dropColumn('refund','applyTime');
        $this->dropColumn('refund','applyMemo');
        $this->dropColumn('refund','status');
        $this->dropColumn('refund','rejectMemo');
        $this->dropColumn('refund','refundMemo');

        $this->addColumn('refund','applyHandlerType','INT(10) NOT NULL DEFAULT 0 COMMENT "申请者类型" AFTER `price`');
        $this->addColumn('refund','applyHandlerId','INT(10) NOT NULL DEFAULT 0 COMMENT "申请者id" AFTER `applyHandlerType`');
        $this->addColumn('refund','applyTime','INT(10) NOT NULL DEFAULT 0 COMMENT "申请时间" AFTER `applyHandlerId`');
        $this->addColumn('refund','applytMemo','VARCHAR(300) DEFAULT NULL COMMENT "申请备注" AFTER `applyTime`');
        
        $this->addColumn('refund','rejectHandlerType','INT(10) DEFAULT NULL COMMENT "驳回者类型" AFTER `applytMemo`');
        $this->addColumn('refund','rejectHandlerId','INT(10) DEFAULT NULL COMMENT "驳回者id" AFTER `rejectHandlerType`');
        $this->addColumn('refund','rejectTime','INT(10) DEFAULT NULL COMMENT "驳回时间" AFTER `rejectHandlerId`');
        $this->addColumn('refund','rejectMemo','VARCHAR(300) DEFAULT NULL COMMENT "驳回备注" AFTER `rejectTime`');

        $this->addColumn('refund','refundHandlerType','INT(10) DEFAULT NULL COMMENT "退款者类型" AFTER `rejectMemo`');
        $this->addColumn('refund','refundHandlerId','INT(10) DEFAULT NULL COMMENT "退款者id" AFTER `refundHandlerType`');
        $this->addColumn('refund','refundTime','INT(10) DEFAULT NULL COMMENT "退款时间" AFTER `refundHandlerId`');
        $this->addColumn('refund','refundMemo','VARCHAR(300) DEFAULT NULL COMMENT "退款备注" AFTER `refundTime`');

        $this->addColumn('refund','status','INT(10) NOT NULL DEFAULT 0 COMMENT "状态(0=申请中/1=已退款/-1=已驳回)" AFTER `refundMemo`');
    }
}
