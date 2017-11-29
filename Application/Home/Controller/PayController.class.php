<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class PayController extends CommonController{

    public $partnerId = 100056;

    public $gateway = 'http://pay.polarthink.com';


    /**
     * 统一下单接口
     * @return string
     */
    public function order() {
        $params['partner_id']   = $this->partnerId;
        $params['order_no']     = (string)rand(1000,9999); //商户自己的订单号
        $params['order_title']  = '充值';
        $params['order_amount'] = 0.01;
        $params['return_url']   = 'http://xxxxx/Pay/payReturn';
        $params['notify_url']   = 'http://xxxxx/Pay/payNotify';
        $params['pay_type']     = 'alipay';
        $params['sign_type']    = 'md5';
        $params['client_ip']    = $_SERVER['REMOTE_ADDR'];
        $params['timestamp']    = time();
        $params['sign']         = $this->signBuild($params); //生成签名

        $this->buildRequestForm($params); //输出表单到页面，提交请求
    }

    /**
     * 获取支付二维码内容
     */
    public function getQrCode() {
        $money = $_GET['money'];
        $pay_orderid = date("YmdHis").rand(1,9);    //订单号
        $order = M("incomelog");
        $data['state'] = 0;
        $data['type'] = 0;
        $data['reson'] = "充值";
        $data['addymd'] = date("Y-m-d H:i:s",time());
        $data['addtime'] = time();
        $data['userid'] = session('uid');
        $data['income'] = $money;
        $data['orderid'] = $pay_orderid;
        $data['cont'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $logid= $order->add($data);

        $params['partner_id']   = $this->partnerId;
        $params['order_no']     = date("YmdHis").rand(100000,999999); //商户自己的订单号
        $params['order_title']  = '充值';
        $params['order_amount'] =$money;
        $params['return_url']   ="http://www.898tj.com/index.php/Home/Login/pay/token/admin123/id/$logid";
        $params['notify_url']   = "http://www.898tj.com/index.php/Home/Login/pay/token/admin123/id/$logid";
        $params['pay_type']     = 'weixin';
        $params['sign_type']    = 'md5';
        $params['client_ip']    = "111.10.32.144";
        $params['timestamp']    = time();
        $params['sign']         = $this->signBuild($params); //生成签名

        $url = $this->gateway . '/api/Pay/getQrCode';
        $response = $this->post($url, $params);
        $responseArr = json_decode($response, true);
        $sign = $this->signBuild($responseArr['data']);
        if ($sign != $responseArr['data']['sign']) {
            exit('签名错误');
        }
        $url = $responseArr['data']['code_url'];
//        print_r($url);
//        $urls ="http://pan.baidu.com/share/qrcode?w=200&h=200&url=$url";
//        $img = curl_get_contents($urls);
        $this->assign('img',$url);
        $this->display('User:recharge');
    }


    public function qrcode(){
        Vendor('phpqrcode.phpqrcode');
        $url=$_GET['url'];
        $object = new \QRcode();
        $level=3;
        $size=5;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    /**
     * 生成签名
     * @param $params
     * @param string $type
     * @return string
     */
    function signBuild($params, $type = 'md5') {
        $string = $this->buildToBeSignedString($params);
        $sign = '';
        if ($type == 'md5') {
            $string .= '&key=c1766ebb25c4c4e23f849932bbd99b91';
            $sign = md5($string);
        }
        return $sign;
    }

    /**
     * 生成待签名字符串
     * 对数组里的每一个值从a到z的顺序排序，若遇到相同首字母，则看第二个字母以此类推。
     * 排序完成后，再把所有数组值以‘&’字符连接起来
     * @param  array $params 待签名参数
     * @return string
     */
    function buildToBeSignedString($params) {
        //sign和空值不参与签名
        ksort($params);
        $stringToBeSigned = '';
        $i = 0;
        foreach ($params as $k => $v) {
            if ($v != '' && $k != 'sign') {
                if ($i == 0) {
                    $stringToBeSigned .= $k . '=' . $v;
                } else {
                    $stringToBeSigned .= '&' . $k . '=' . $v;
                }
                $i++;
            }
        }

        return $stringToBeSigned;
    }

    /**
     * post请求
     * @param $url
     * @param $params
     * @param array $header
     * @param int $timeOut
     * @return mixed
     */
    public function post($url, $params, $header = array(), $timeOut = 10) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        if (1 == strpos('$' . $url, 'https://')) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 建立请求，以表单HTML形式构造（默认），自动提交表单
     * @param  array $params 请求参数
     * @param  string $method GET OR POST
     * @return string
     */
    public function buildRequestForm($params, $method = 'POST') {
        //请求地址
        $action = $this->gateway . '/api/pay';

        $html = "<form id='submit' name='submit' action='" . $action . "' method='" . $method . "'>";
        while(list($key, $val) = each($params)) {
            if (!empty($val)) {
                $val = str_replace("'", '&apos;', $val);
                $html .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
            }
        }
        $html .= "<input type='submit' value='ok' style='display:none;'></form>";
        $html .= "<script>document.forms['submit'].submit();</script>";
        print_r($html);die;
        echo $html;
    }
}