<?php
use yii\db\Migration;
class m180312_025454_add_column_uniqueId_to_sku_table extends Migration{
    public function up(){
        $this->addColumn('sku','uniqueId','VARCHAR(200) NOT NULL COMMENT "sku唯一编号" AFTER `id`');
        //$this->createIndex('skuUniqueId','sku','uniqueId',true);
    }
}