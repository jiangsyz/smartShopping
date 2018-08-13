<?php
use yii\db\Migration;
class m180813_072512_add_column_to_sku_table extends Migration{
    public function up(){
        $this->addColumn('sku','deliverTime','INT(10) DEFAULT NULL COMMENT "发货时间" AFTER `count`');
    }
}
