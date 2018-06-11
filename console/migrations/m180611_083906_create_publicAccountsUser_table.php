<?php
use yii\db\Migration;
class m180611_083906_create_publicAccountsUser_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="公众号用户"';
        }
        //建表
        $this->createTable(
            'public_account_user',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'appid'=>$this->string(200)->notNull()->comment('公众号appid'),
                'openid'=>$this->string(200)->notNull()->comment('openid'),
                'unionid'=>$this->string(200)->defaultValue(NULL)->comment('unionid'),
                'time'=>$this->integer(10)->notNull()->comment('记录添加时间'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('openidUnique','public_account_user','appid,openid',true);
        $this->createIndex('unionidUnique','public_account_user','appid,unionid',true);
    }
}
