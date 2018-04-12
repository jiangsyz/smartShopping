<?php
use yii\db\Migration;
class m180412_025553_change_column_to_orderRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_record','backKeepCountStatus','INT(10) NOT NULL COMMENT "是否返过库存(0=没返/1=已返)" AFTER `finishStatus`');
    }
}
