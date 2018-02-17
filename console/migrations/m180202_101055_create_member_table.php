<?php
use yii\db\Migration;
class m180202_101055_create_member_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="会员"';
        }
        //建表
        $this->createTable(
            'member',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'phone'=>$this->string(200)->notNull()->unique()->comment('会员手机'),
                'nickName'=>$this->string(200)->defaultValue(NULL)->comment('昵称'),
                'avatar'=>$this->string(200)->defaultValue(NULL)->comment('头像'),
                'wechatUnionID'=>$this->string(200)->defaultValue(NULL)->unique()->comment('微信UnionID'),
                'wechatOpenID'=>$this->string(200)->defaultValue(NULL)->unique()->comment('微信OpenID'),
                'createTime'=>$this->integer(10)->notNull()->comment('记录创建时间'),
                'locked'=>$this->integer(10)->notNull()->defaultValue(0)->comment('锁定(0=正常/1=锁定)'),
            ),
            $options
        );
    }
}
