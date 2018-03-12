<?php
use yii\db\Migration;
class m180312_025442_add_column_uniqueId_to_spu_table extends Migration{
    public function up(){
        $this->addColumn('spu','uniqueId','VARCHAR(200) NOT NULL COMMENT "spu唯一编号" AFTER `id`');
        $this->execute("UPDATE `spu` SET `uniqueId`=`id` WHERE 1;");
        $this->createIndex('spuUniqueId','spu','uniqueId',true);
    }
}
