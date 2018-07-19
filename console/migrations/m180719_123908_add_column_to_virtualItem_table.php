<?php
use yii\db\Migration;
class m180719_123908_add_column_to_virtualItem_table extends Migration{
    public function up(){
        $this->addColumn('virtual_item','sort','INT(10) DEFAULT 0 COMMENT "排序" AFTER `locked`');
    }
}
