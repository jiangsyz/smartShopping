<?php
use yii\db\Migration;
class m180323_080715_change_column_to_orderRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_record','memberPrice','DOUBLE NOT NULL COMMENT "会员价" AFTER `price`');
        $this->addColumn('order_record','reduction','DOUBLE NOT NULL COMMENT "节约的金额" AFTER `finalPrice`');
        $this->alterColumn('order_record','price','DOUBLE NOT NULL COMMENT "非会员价"');
    }
}
