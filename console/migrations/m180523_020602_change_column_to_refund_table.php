<?php
use yii\db\Migration;
class m180523_020602_change_column_to_refund_table extends Migration{
    public function up(){
        $this->alterColumn('refund','status','INT(10) NOT NULL DEFAULT 0 COMMENT "-1=驳回/0=待办/1=打款中/2=打款成功/3=打款失败"');
    }
}