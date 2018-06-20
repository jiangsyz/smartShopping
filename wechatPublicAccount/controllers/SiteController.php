<?php
namespace wechatPublicAccount\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\member\publicAccountUser;
class SiteController extends SmartWebController{
    public $enableCsrfValidation=false;
    //========================================
    public function actionIndex(){
        $token='zs2018WLJFT';
    	$echostr=$this->requestGet('echostr','');
    	$signature=$this->requestGet('signature','');
		$timestamp=$this->requestGet('timestamp','');
		$nonce=$this->requestGet('nonce','');
        //形成数组,然后按字典序排序
		$array=array($nonce,$timestamp,$token);
		sort($array);
		$array=implode($array);
        //拼接成字符串,sha1加密,然后与signature进行校验
		$str=sha1($array);
        //第一次接入,微信会验证token
		if($str==$signature && $echostr){
			header('content-type:text');
			ob_clean();
        	echo $echostr;
        	exit;
    	}
    	else{
    	   try{
                //开启事务
                $trascation=Yii::$app->db->beginTransaction();
                //获取回调数据
                $data=file_get_contents('php://input');
                //解析xml
                libxml_disable_entity_loader(true);
                $data=simplexml_load_string($data,'SimpleXMLElement',LIBXML_NOCDATA);
                $data=json_decode(json_encode($data),true);
                if(!isset($data['FromUserName'])) throw new SmartException("miss FromUserName");
                if(!isset($data['ToUserName'])) throw new SmartException("miss ToUserName");
                //通过公众号appId
                $appId=Yii::$app->params["app2"]["appId"];
                //查询是否是老用户
                $where="`appid`='{$appId}' AND `openid`='{$data['FromUserName']}'";
                $publicAccountUser=publicAccountUser::find()->where($where)->one();
                //新用户加入公众号用户列表
                if(!$publicAccountUser){
                    $publicAccountUserData=array();
                    $publicAccountUserData['appid']=$appId;
                    $publicAccountUserData['openid']=$data['FromUserName'];
                    $publicAccountUserData['unionid']=NULL;
                    publicAccountUser::addObj($publicAccountUserData);
                }
                //提交事务
                $trascation->commit();
                //返回应答
                $time=time();
                $str=
                "
                <xml>
                <ToUserName><![CDATA[{$data['FromUserName']}]]></ToUserName>
                <FromUserName><![CDATA[{$data['ToUserName']}]]></FromUserName>
                <CreateTime>{$time}</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[正善牛肉欢迎您!]]></Content>
                </xml>
                ";
                $this->response(3,$str);
            }
            catch(Exception $e){
                //回滚
                $trascation->rollback();
                $this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
            }
    	}
    }
}