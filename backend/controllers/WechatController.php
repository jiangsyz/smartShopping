<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\model\source;
use backend\models\member\member;
use backend\models\source\sourceProperty;
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
            //获取上线id
            $referenceId=$this->requestPost('referenceId',"");
            //通过jscode获取sessionKey和openid
            $appId=Yii::$app->params["app1"]["appId"];
            $appSecret=Yii::$app->params["app1"]["appSecret"];
            $data=Yii::$app->smartWechat->jscode2session($appId,$appSecret,$jscode);
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
            //用户不存在创建用户,并绑定上线
            if(!$member){
                $member=member::addObj(array('phone'=>$phone));
                $member->addProperty("referenceId",$referenceId);
            }
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
    public function actionApiGetUnionid(){
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
            $appId=Yii::$app->params["app1"]["appId"];
            $appSecret=Yii::$app->params["app1"]["appSecret"];
            $data=Yii::$app->smartWechat->jscode2session($appId,$appSecret,$jscode);
            //获取sessionKey
            $sessionKey=$data['sessionKey'];
            //获取openid
            $openid=$data['openid'];
            //解密用户加密信息
            $data=Yii::$app->smartWechat->encrypteData($sessionKey,$iv,$encryptedData);
            //日志
            Yii::$app->smartLog->debugLog(json_encode($data));
            //提取手机号
            if(!isset($data['unionId'])) throw new SmartException("miss unionId");
            $unionid=$data['unionId'];
            //获取通过openid获取会员
            $sourceType=source::TYPE_MEMBER;
            $where="`sourceType`='{$sourceType}' AND `propertyKey`='openid' AND `propertyVal`='{$openid}'";
            $property=sourceProperty::find()->where($where)->one();
            if(!$property) throw new SmartException("miss property");
            //获取会员
            $member=member::getSource(source::TYPE_MEMBER,$property->sourceId,true);
            if(!$member) throw new SmartException("miss member");
            //绑定unionid
            if($member->getProperty("unionid")===NULL) $member->addProperty("unionid",$unionid);
            //提交事务
            $trascation->commit();
            //返回
            $this->response(1,array('error'=>0));
        }
        catch(Exception $e){
            //回滚
            $trascation->rollback();
            $this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
        }
    }
}