<?php
//令牌管理
namespace backend\models\token;
use Yii;
use yii\base\Component;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
abstract class tokenManagement extends Component{
	//获取令牌的拥有者
	abstract public function getOwner();
	//========================================
	//令牌
	public $token=false;
	//========================================
	//获取令牌管理器
	public static function getManagement($tokenStr,$type=array()){
		//通过令牌文本和需要获取的令牌类型,查询令牌
		$token=Yii::$app->smartToken->getToken($tokenStr,$type);
		if(!$token) throw new SmartException("miss token");
		//根据不同的令牌类型返回对应的令牌管理器
		if($token->type==source::TYPE_MEMBER) return new memberManagement(array('token'=>$token));
		if($token->type==source::TYPE_STAFF) return new staffManagement(array('token'=>$token));
		throw new SmartException("miss TokenManagement");
	}
	//========================================
	//创建令牌
	public static function createToken($type,$data){
		return Yii::$app->smartToken->createToken($type,$data);
	}
}