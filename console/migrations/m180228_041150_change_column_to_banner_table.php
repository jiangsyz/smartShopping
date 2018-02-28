<?php
use yii\db\Migration;
class m180228_041150_change_column_to_banner_table extends Migration{
    public function up(){
        $this->dropColumn('banner','sourceType');
        $this->dropColumn('banner','sourceId');
        $this->addColumn('banner','uri','VARCHAR(200) NOT NULL COMMENT "跳转链接" AFTER `image`');
    }
}
