<?php
use yii\db\Migration;
class m180204_155540_create_staff_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="员工"';
        }
        //建表
        $this->createTable(
            'staff',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'phone'=>$this->string(200)->notNull()->unique()->comment('会员手机'),
                'name'=>$this->string(200)->notNull()->comment('姓名'),
                'locked'=>$this->integer(10)->notNull()->defaultValue(0)->comment('锁定(0=正常/1=锁定)'),
            ),
            $options
        );
    }
}
