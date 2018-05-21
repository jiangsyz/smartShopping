<?php
namespace wechatRefund\controllers;
use Yii;
use yii\web\SmartWebController;
use wechatRefund\models\refundCallback;
use backend\models\order\refund;
class SiteController extends SmartWebController{
    public $enableCsrfValidation=false;
    //========================================
    //退款
    public function actionIndex(){
        try{
            $callbackLog=false;
            //开启事务
            $trascation=Yii::$app->db->beginTransaction();
            //获取回调数据
            $data=file_get_contents('php://input');
            //解析xml
            libxml_disable_entity_loader(true);
            $data=simplexml_load_string($data,'SimpleXMLElement',LIBXML_NOCDATA);
            $data=json_decode(json_encode($data),true);
            if(!is_array($data)) throw new SmartException("data is not array");
            //记录日志
            $log=array();
            $log['runningId']=$this->runningId;
            $log['payType']='wechat';
            $log['callBackData']=json_encode($data);
            $callbackLog=refundCallback::addObj($log);
            //判断返回状态码
            if(!isset($data['return_code'])) throw new SmartException("data miss return_code");
            if($data['return_code']!='SUCCESS') throw new SmartException("data return_code is not success");
            //判断商户号
            if(!isset($data['mch_id'])) throw new SmartException("data miss mch_id");
            //判断加密信息
            if(!isset($data['req_info'])) throw new SmartException("data miss req_info");
            //获取退款结果
            $result=Yii::$app->smartWechatPay->getRefundResult($data['mch_id'],$data['req_info']);
            $result=simplexml_load_string($result,'SimpleXMLElement',LIBXML_NOCDATA);
            $result=json_decode(json_encode($result),true);
            if(!is_array($result)) throw new SmartException("result is not array");
            //判断退款流水号
            if(!isset($result['out_refund_no'])) throw new SmartException("result miss out_refund_no");
            //判断退款结果
            if(!isset($result['refund_status'])) throw new SmartException("result miss refund_status");
            //通过流水号查找打款中的退款
            $table=refund::tableName();
            $sql="SELECT * FROM {$table} WHERE `refundMemo`='{$result['out_refund_no']}' AND `status`='1' FOR UPDATE";
            $refund=refund::findBySql($sql)->one();
            if(!$refund) throw new SmartException("miss refund");
            //修改状态
            if($result['refund_status']=='SUCCESS'){
                $refund->updateObj(array('status'=>refund::STATUS_REFUND_SUCCESS));
                $callbackLog->updateObj(array('status'=>1,'memo'=>json_encode($result)));
            }
            else{
                $refund->updateObj(array('status'=>refund::STATUS_REFUND_FAIL));
                $callbackLog->updateObj(array('status'=>-1,'memo'=>json_encode($result)));
            }
            //提交事务
            $trascation->commit();
            //返回验证码订单号
            $this->response(1,array('error'=>0));
        }
        catch(Exception $e){
            //修改日志状态
            if($callbackLog) $callbackLog->updateObj(array('status'=>-1,'memo'=>$e->getMessage()));
            //回滚
            $trascation->rollback();
            $this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
        }
    }
    //========================================
    /*
    public function actionIndex(){
        $mchId='1500307292';
        $req_info="hsSKQR5LPhqaI0KoMfGPNhOP+xI+q6MjzvJ7sYgy9svx27wJnxwdJBrviVS3YXTSed\/tEqycvx9\/qetIFnHGOf5KQYgIWEWmsqYyt2JP+R9zE3U0wzrtKKDs1V+lhcIAYX+aoANFLGgBN8evKVdK8W4sXyET4OkoxAK3E\/ir6nQpV2XrFCnrnmGURhLX5Q\/Tpfkaz+ng2eAJriIvYzSW9NezHj9x+Up14zI14wVezMwBLX32HpBCUCDrF+Wirb+soV4DQQXtjk+f856y6Rz0pgyvvDJBT6sYlbUnmr8ses9NK3gZEEgZjfJGpVtKVxlptrMfiZ9AuqYTQrPhUXOXLg1E8UtB\/pH014axCLn7G48MwUD4uuNOH68MUu3Wd11p77ns13Z67yjrqSIDVQqS8XyQY5q9gOcH4WLPu7N9XZwRgATG0OSIleb8iF7mMynOqQhwAoUtJ0UCey3eNWwZWhje3fxpmDnaxA9bhYRXYbroFy1JP3hE\/c3+dF6JDAggMHSK35DQ2ruMEmLEyneTjG4t9\/v92P5Jqm\/H83pwJOkn6\/szQhFGHScjb4rwtOlHo4q0kaUyu4TnrH397RuAVRwAZncO5Hvb7zmQ10ThxqkloH+9kg0gZgGH6h7ZcdxB9\/RYon4BFjy1LA86x7DH+R1DwOgiVenFY+7lRIt3Zzc1m7uppEu5skB9DSTb5zf\/zI2W+SWDivX03mjgRXmieoT6Vh3rrdpavguQWjSAxi505ZomGN05Rz22htcX\/ZCZqLCcxMw+H9s2XIhOE0JUiRSYUmmDPoQNoGFRqT57EXbGf2KBucpvi2a7u20KZ\/T2DHFq7bJ5cGAINsjZrmsu63RGv18kjea88oavSFWbzA75kWJOuqWFSryYzbfNmqXMJ27A30wOv29NhkOsqWYfDqn0TYScFaJECy41MoV+ZzByg+O5kh\/on6Vabbn\/nIulEgMD8YT5ofQGFDXA0qT7RbyEC5Lt11kOkO\/ifjWyDphcrA30JUZee\/zX2C1k\/fxoUr4CqnqGMdVjVThr7nO\/XZwmiOa9rr+OWm87tWxxnp8=";
        $data=Yii::$app->smartWechatPay->getRefundResult($mchId,$req_info);
        //解析xml
        libxml_disable_entity_loader(true);
        $data=simplexml_load_string($data,'SimpleXMLElement',LIBXML_NOCDATA);
        $data=json_decode(json_encode($data),true);
        if(!is_array($data)) throw new SmartException("data is not array");
        var_dump($data);
    }
    */
}
