<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\model\source;
use backend\models\member\member;
class WechatController extends SmartWebController{
    public $enableCsrfValidation=false;
    //========================================
    public function actionApiAuth(){
        try{
            //开启事务
            $trascation=Yii::$app->db->beginTransaction();
            //获取jscode
            $jscode=Yii::$app->request->post('jscode',"");
            //获取加密的用户数据
            $encryptedData=Yii::$app->request->post('encryptedData',"");
            //获取加密算法的初始向量
            $iv=Yii::$app->request->post('iv',"");
            //通过jscode获取sessionKey和openid
            $data=Yii::$app->smartWechat->jscode2session($jscode);
            //获取sessionKey
            $sessionKey=$data['sessionKey'];
            //获取openid
            $openid=$data['openid'];
            //解密用户加密信息
            $data=Yii::$app->smartWechat->encrypteData($sessionKey,$iv,$encryptedData);
            //提取手机号
            if(!isset($data['purePhoneNumber'])) throw new SmartException("miss purePhoneNumber");
            $phone=$data['purePhoneNumber'];
            //获取会员
            $tableName=member::tableName();
            $sql="SELECT * FROM {$tableName} where `phone`='{$phone}' FOR UPDATE";
            $member=member::findBySql($sql)->one();
            //用户不存在创建用户
            if(!$member) $member=member::addObj(array('phone'=>$phone));
            //绑定openid
            if($member->getProperty("openid")===NULL) $member->addProperty("openid",$openid);
            //提交事务
            $trascation->commit();
            //返回
            $this->response(1,array('error'=>0,'data'=>array('phone'=>$phone)));
        }
        catch(Exception $e){
            //回滚
            $trascation->rollback();
            $this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
        }
    }
}