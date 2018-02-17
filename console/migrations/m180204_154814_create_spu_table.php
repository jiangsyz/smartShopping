<?php
use yii\db\Migration;
class m180204_154814_create_spu_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="标准售卖单位"';
        }
        //建表
        $this->createTable(
            'spu',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'title'=>$this->string(200)->notNull()->unique()->comment('标题'),
                'desc'=>$this->string(200)->defaultValue(NULL)->comment('描述'),
                'cover'=>$this->string(200)->defaultValue(NULL)->comment('封面图'),
                'freight'=>$this->double()->notNull()->comment('运费'),
                'detail'=>$this->text()->defaultValue(NULL)->comment('商品详情'),
                'uri'=>$this->string(200)->defaultValue(NULL)->comment('详情uri'),
                'memberId'=>$this->integer(10)->notNull()->comment('推荐人id'),
                'closed'=>$this->integer(10)->notNull()->defaultValue(0)->comment('是否下架(0=上架/1=下架)'),
                'locked'=>$this->integer(10)->notNull()->defaultValue(0)->comment('锁定(0=正常/1=锁定)'),
            ),
            $options
        );
    }
}
