<?php
use yii\db\Migration;
class m180202_053340_create_address_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="会员地址"';
        }
        //建表
        $this->createTable(
            'address',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'memberId'=>$this->integer(10)->notNull()->comment('会员id'),
                'name'=>$this->string(200)->notNull()->comment('收件人姓名'),
                'phone'=>$this->string(200)->notNull()->comment('收件人手机'),
                'areaId'=>$this->integer(10)->notNull()->comment('收件人区域id'),
                'address'=>$this->string(200)->notNull()->comment('收件人详细地址'),
                'postCode'=>$this->string(200)->defaultValue(NULL)->comment('邮编'),
                'createTime'=>$this->integer(10)->notNull()->comment('记录创建时间'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('addressUnique','address','memberId,areaId,address,name,phone',true);
    }
}
