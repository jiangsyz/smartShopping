<?php
use yii\db\Migration;
class m180327_093818_change_column_to_sourceProperty_table extends Migration{
    public function up(){
        $this->addColumn('source_property','createTime','INT(10) NOT NULL COMMENT "添加时间" AFTER `propertyVal`');
    }
}
