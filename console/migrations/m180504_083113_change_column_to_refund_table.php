<?php
use yii\db\Migration;
class m180504_083113_change_column_to_refund_table extends Migration{
    public function up(){
        $this->dropColumn('refund','applytMemo');
        $this->addColumn('refund','applyMemo','VARCHAR(300) DEFAULT NULL COMMENT "申请备注" AFTER `applyTime`');
    }
}
