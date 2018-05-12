<?php
namespace backend\controllers;

use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;

class ForluluController extends SmartWebController
{
	//最好放在errorCode的model中，此处是伪代码，不做迁移
	public static $OK = 0;
	public static $IllegalAesKey = -41001;
	public static $IllegalIv = -41002;
	public static $IllegalBuffer = -41003;
	public static $DecodeBase64Error = -41004;

	//伪代码  暂时写死在这里 最好放入配置文件
	const APPID = 'wx4fadd384b39658cd';

	/*解密wx.getUserInfo返回的encryptedData
	TODO 微信官方建议为了数据不被篡改，开发者不应该把session_key传到小程序客户端等服务器外的环境*/
	public function actionEncrypteData()
	{
		try{
			$sessionKey = Yii::$app->request->post('sessionKey');
			$appId = self::APPID;
			//加密的用户数据
			$encryptedData = Yii::$app->request->post('encryptedData');
			//与用户数据一同返回的初始向量
			$iv = Yii::$app->request->post('iv');
			if (strlen($sessionKey) != 24) {
				return self::$IllegalAesKey;
			}
			$aesKey = base64_decode($sessionKey);

			if (strlen($iv) != 24) {
				return self::$IllegalIv;
			}
			$aesIV = base64_decode($iv);
			$aesCipher = base64_decode($encryptedData);
			$result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

			$dataObj = json_decode($result);
			if($dataObj  == NULL) {
				return self::$IllegalBuffer;
			}
			if($dataObj->watermark->appid != $appId) {
				return self::$IllegalBuffer;
			}
			$this->response(1,array('error'=>0,'data'=>$result));
    	}
    	catch(Exception $e) {
    		$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
    	}
	}
}