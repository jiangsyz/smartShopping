<?php
use yii\db\Migration;
class m180315_030902_add_column_createTime_to_spu_table extends Migration{
    public function up(){
        $this->addColumn('spu','createTime','INT(0) NOT NULL COMMENT "创建时间" AFTER `locked`');
    }
}
