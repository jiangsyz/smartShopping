<?php
use yii\db\Migration;
class m180301_075413_drop_index_bannerUnique_to_banner_table extends Migration{
    public function up(){
        $this->dropIndex('bannerUnique','banner');
    }
}
