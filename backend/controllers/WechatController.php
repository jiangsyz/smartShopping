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
            $jscode=$this->requestPost('jscode',"");
            //获取加密的用户数据
            $encryptedData=$this->requestPost('encryptedData',"");
            //获取加密算法的初始向量
            $iv=$this->requestPost('iv',"");
            //通过jscode获取sessionKey和openid
            $data=Yii::$app->smartWechat->jscode2session($jscode);
            //获取sessionKey
            $sessionKey=$data['sessionKey'];
            //获取openid
            $openid=$data['openid'];
            //解密用户加密信息
            $data=Yii::$app->smartWechat->encrypteData($sessionKey,$iv,$encryptedData);
            //日志
            Yii::$app->smartLog->debugLog(json_encode($data));
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
    //========================================
    public function actionIndex(){
        $encryptedData="5D6p5giaozmyFyh2AtPGuGxM0Vj9deY5V2LG+xN6wppy870N+dtP2o+QQ9BY\/Fhx0rT+Ogg2f8elCIfdQ5b0CCbU107lfcQ0sG2cza7nO2WeLQi6o0JwoReUw0llBQ68HbhiLS3ZB1Y6aE8KiRnqgQiYtcNhQE2Yq\/96QMy6w0E19u\/1tXDlSJa725RReLzw\/TyMZJagIqApDzZTaVMOIA==";
        $iv="jZkcHgGUEQIS7fnp9BlYFQ==";
        $sessionKey="gAIbqA642JtOJZJuNm1nMQ==";
        $data=Yii::$app->smartWechat->encrypteData($sessionKey,$iv,$encryptedData);

        var_dump($data);
    }
}