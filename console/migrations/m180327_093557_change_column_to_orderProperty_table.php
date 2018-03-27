<?php
use yii\db\Migration;
class m180327_093557_change_column_to_orderProperty_table extends Migration{
    public function up(){
        $this->addColumn('order_property','createTime','INT(10) NOT NULL COMMENT "添加时间" AFTER `propertyVal`');
    }
}
