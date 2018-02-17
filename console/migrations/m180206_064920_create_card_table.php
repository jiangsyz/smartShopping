<?php
use yii\db\Migration;
class m180206_064920_create_card_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="虚拟产品"';
        }
        //建表
        $this->createTable(
            'card',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'title'=>$this->string(200)->notNull()->unique()->comment('标题'),
                'desc'=>$this->string(200)->defaultValue(NULL)->comment('描述'),
                'cover'=>$this->string(200)->defaultValue(NULL)->comment('封面'),
                'price'=>$this->double()->notNull()->comment('价格'),
                'memberId'=>$this->integer(10)->notNull()->comment('推荐人id'),
                'closed'=>$this->integer(10)->notNull()->defaultValue(0)->comment('是否下架(0=上架/1=下架)'),
                'locked'=>$this->integer(10)->notNull()->defaultValue(0)->comment('锁定(0=正常/1=锁定)'),
            ),
            $options
        );
    }
}
