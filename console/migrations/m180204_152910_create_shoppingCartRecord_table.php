<?php
use yii\db\Migration;
class m180204_152910_create_shoppingCartRecord_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="购物车记录"';
        }
        //建表
        $this->createTable(
            'shopping_cart_record',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'memberId'=>$this->integer(10)->notNull()->comment('会员id'),
                'sourceType'=>$this->integer(10)->notNull()->comment('资源类型'),
                'sourceId'=>$this->integer(10)->notNull()->comment('资源id'),
                'count'=>$this->integer(10)->notNull()->comment('购买数量'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('shoppingCartRecordUnique','shopping_cart_record','memberId,sourceType,sourceId',true);
    }
}
