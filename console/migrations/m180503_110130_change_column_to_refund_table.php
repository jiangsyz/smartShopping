<?php
use yii\db\Migration;
class m180503_110130_change_column_to_refund_table extends Migration{
    public function up(){
        $this->alterColumn('refund','price','INT(10) NOT NULL DEFAULT 0 COMMENT "退款金额(单位为分)"');

    }
}
