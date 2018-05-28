<?php
use yii\db\Migration;
class m180528_063251_add_column_to_logistics_table extends Migration{
    public function up(){
        $this->addColumn('logistics','code','VARCHAR(200) DEFAULT NULL COMMENT "物流编号" AFTER `name`');
        $this->addColumn('logistics','phone','VARCHAR(200) DEFAULT NULL COMMENT "物流公司电话" AFTER `code`');
    }
}
