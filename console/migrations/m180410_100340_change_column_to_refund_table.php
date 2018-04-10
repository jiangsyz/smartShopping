<?php
use yii\db\Migration;
class m180410_100340_change_column_to_refund_table extends Migration{
    public function up(){
        $this->dropColumn('refund','price');
        $this->addColumn('refund','price','INT(10) NOT NULL COMMENT "退款金额(分为单位)" AFTER `bid`');
    }
}
