<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class UserController extends CommonController{

    public function isquan(){
        $quannum = trim($_GET['num']);
        $juan =M('quan')->where(array('cont'=>$quannum))->find();
        if($juan['id']){
            if($juan['state'] != 1){
                echo 2;exit();
            }
            echo 1;
        }else{
            echo 0;
        }

    }

    public function upgradeType(){ // 1 成功 2密码不对 3金额错误 4余额不足 5最高会员 6升级类型错误
        $menber =M('menber');
        $res_user =$menber->where(array('uid'=>session('uid')))->find();
        if($_GET['pwd'] !=$res_user['pwd2']){
            echo 2;exit();
        }



        if($res_user['type'] == 4){
            echo 5;exit();
        }

        $quannum = trim($_GET['num']);
        $juan =M('quan')->where(array('cont'=>$quannum))->find();
        $price = 0;
        if($juan['state'] == 1){
            $price =$juan['price'];
        }

        if($_GET['rank'] <=$res_user['type']){
            echo 6;exit();
        }


        // 处理金额
        $noewmoney = $this->chaneformoney($res_user['type']);
        $aftermoney =$this->chaneformoney($_GET['rank']);
        $_GET['needmoney'] =$aftermoney - $noewmoney;
        if($_GET['needmoney'] < 300){
            echo 3;exit();
        }

        $income =M('incomelog');
        $data1['type'] =4;
        $data1['state'] =2;
        $data1['reson'] ='会员升级';
        $data1['addymd'] =date('Y-m-d',time());
        $data1['addtime'] =time();
        $data1['userid'] =session('uid');
        $data1['income'] =$_GET['needmoney'];
        $type =$res_user['type']+1;
        if($juan['price'] == $_GET['needmoney']){
            $menber->where(array('uid'=>session('uid')))->save(array('type'=>$_GET['rank']));
            $data1['reson'] ='会员升级(代金券)';
            $income->add($data1);
            M('quan')->where(array('cont'=>$quannum))->save(array('state'=>2));
            echo 1;exit();
        }else{
            $nextneed =bcsub($_GET['needmoney'],$price,2);
            if($res_user['chargebag'] < $nextneed){
                echo 4;exit();
            }
            if($juan['state'] == 1){
                M('quan')->where(array('cont'=>$quannum))->save(array('state'=>2));
            }
            $chargebag =bcsub($res_user['chargebag'],$nextneed,2);
            $menber->where(array('uid'=>session('uid')))->save(array('type'=>$type,'chargebag'=>$chargebag));
        }
        echo 1;
    }

    public function upgrade(){
        $menber =M('menber');
        $res_user =$menber->where(array('uid'=>session('uid')))->find();
        $type =$this->chanefortype($res_user['type']+1);
        $typemoney =$this->chaneformoney($res_user['type']+1);

        // 处理金额
        $noewmoney = $this->chaneformoney($res_user['type']);

        $typemoney= $typemoney - $noewmoney;
        if($typemoney < 0){
            $typemoney = 0;
        }

        $this->assign('typename',$type);
        $this->assign('typemoney',$typemoney);
        $this->display();
    }
    public function workOrder(){
        $incomelog =M('incomelog');
        $con['userid'] = session('uid');
        $con['type']   =11;
        $con['state']   =array('in',array(1,2));
        $res = $incomelog->where($con)->order(" id DESC ")->limit(18)->select();

        $this->assign('res',$res);
        $this->display();
    }

    public function about(){
        $this->display();
    }

    public function contact(){
        $this->display();
    }


    private function chaneformoney($type){
        if($type ==0){
            return 0;
        }
        else if($type==1){
            return "699";
        }elseif($type==2){
            return "1399";
        }elseif($type==3){
            return "1999";
        }elseif($type==4){
            return "2699";
        }else{
            return "暂无";
        }
    }

    private function chanefortype($type){
        if($type==1){
            return "普通";
        }elseif($type==2){
            return "高级";
        }elseif($type==3){
            return "豪华";
        }elseif($type==4){
            return "至尊";
        }else{
            return "至尊";
        }
    }


    public function payPwd(){
        if($_POST['pwd'] &&$_POST['pwd2'] ){
            $menber =M("menber");
            $userinfo =$menber->where(array('uid'=>session('uid')))->find();
            if ($userinfo['pwd2'] != $_POST['pwd']){
                echo "<script>alert('当前密码错误');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/payPwd';";
                echo "</script>";
                exit;
            }

            if($_POST['pwd2'] != $_POST['pwd22'] ){
                echo "<script>alert('前后密码不一致');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/payPwd';";
                echo "</script>";
                exit;
            }
            $menber->where(array('uid'=>session('uid')))->save(array('pwd2'=>$_POST['pwd2']));
            echo "<script>alert('支付密码修改成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/payPwd';";
            echo "</script>";
            exit;

        }
        $this->display();
    }

    public function loginPwd(){
        if($_POST['pwd'] &&$_POST['pwd2'] ){
            $menber =M("menber");
            $userinfo =$menber->where(array('uid'=>session('uid')))->find();
            if ($userinfo['pwd'] != $_POST['pwd']){
                echo "<script>alert('当前密码错误');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/loginPwd';";
                echo "</script>";
                exit;
            }

            if($_POST['pwd2'] != $_POST['pwd22'] ){
                echo "<script>alert('前后密码不一致');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/loginPwd';";
                echo "</script>";
                exit;
            }
            $menber->where(array('uid'=>session('uid')))->save(array('pwd'=>$_POST['pwd2']));
            echo "<script>alert('登录密码修改成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/loginPwd';";
            echo "</script>";
            exit;

        }
        $this->display();
    }


    public function popularize(){
        $url = "http://".$_SERVER['SERVER_NAME']."/index.php/Home/Login/reg/fid/".session('uid').".html";
        $this->assign('url',$url);
        $this->display();
    }


    public function my(){
        $menber =M("menber");
        $userinfo =$menber->where(array('uid'=>session('uid')))->find();
        $orderlog = M("orderlog")->where(array('userid'=>session('uid'),'type'=>10))->order('logid DESC')->find();
        // to du
        if($orderlog['logid']){
            if($orderlog['state']==1){
                $msg = "未发货";
            }else{
                $msg = "已发货";
            }
        }else{
            $msg = "暂无信息";
        }
        $con['userid'] =session('uid');
        $con['type'] = array('in','1,2,3,4');
        $con['state'] =1;
        $niu =M("orderlog")->where($con)->count();

        //总金币
        $income =M("orderlog")->where($con)->sum('price');
        $allfan =$income * 1.6;

        // 已返回总金币
        $map['userid'] =session('uid');
        $map['type'] = array('in','10,11,12,13');
        $map['state'] =1;
       $incomes = (float)M("incomelog")->where($map)->sum('income');

        //剩下没返回的
        $left =bcsub ($allfan,$incomes,2);

        // 牛气奖
        $xiaoniuqi = M('incomelog')->where(array('userid'=>session('uid'),'type'=>4,'state'=>2))->sum('income');
        if(!$xiaoniuqi){
            $xiaoniuqi =0;
        }
        $sheng = bcsub($userinfo['niuqi'],$xiaoniuqi,2);
        $this->assign('xiaoniuqi',$xiaoniuqi);
        $this->assign('sheng',$sheng);

        $this->assign('allfan',$allfan);
        $this->assign('incomes',$incomes);
        $this->assign('left',$left);
        $this->assign('niu',$niu);
        $this->assign('msg',$msg);
        $this->display();
    }



    public function sale_buy(){
        if(!$_GET['id']){
            echo "<script>alert('ID异常');";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/sale_list';";
            echo "</script>";
            exit;
        }
        $incomelog =M("incomelog")->where(array('id'=>$_GET['id']))->find();
        $this->assign('res',$incomelog);
        $this->display();
    }

    public function buylog(){
        if(!$_GET['id']){
            echo "<script>alert('ID异常');";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/sale_list';";
            echo "</script>";
            exit;
        }


        $incomelog =M("incomelog")->where(array('id'=>$_GET['id']))->find();
        $state =$incomelog['state']+1;
        M("incomelog")->where(array('commitid'=>$incomelog['commitid']))->save(array('state'=>$state));
        if($state==6){
           $buyer = M("incomelog")->where(array('commitid'=>$incomelog['commitid'],'orderid'=>2))->find();
           //查询用户id上线
           if($this->isshang($buyer['userid'])){
               M("incomelog")->where(array('commitid'=>$incomelog['commitid']))->save(array('state'=>5));
               echo "<script>alert('买家收益今天已达上限');";
               echo "window.location.href='".__ROOT__."/index.php/Home/User/sale_list';";
               echo "</script>";
               exit;
           }
            $userinfo =M("menber")->where(array('uid'=>$buyer['userid']))->find();
            $left = bcadd($userinfo['chargebag'],$incomelog['income'],2);
            M("menber")->where(array('uid'=>$buyer['userid']))->save(array('chargebag'=>$left));
        }
        echo "<script>alert('操作成功');";
        echo "window.location.href='".__ROOT__."/index.php/Home/User/sale_list';";
        echo "</script>";
        exit;
    }


    public function reg(){  //注册下级
        if($_POST['tel']&&$_POST['pwd']){
            if(preg_match("/^1[34578]{1}\d{9}$/",$_POST['tel'])){

            }else{
                echo "<script>alert('请用手机号码注册');";
                if($_POST['num'] == 100){
                    echo "window.location.href='".__ROOT__."/index.php/Home/User/reg100';";
                }else{
                    echo "window.location.href='".__ROOT__."/index.php/Home/User/reg200';";
                }
                echo "</script>";
                exit;
            }
            if($_POST['pwd']!=$_POST['pwd11']){
                echo "<script>alert('密码不一致');";
                if($_POST['num'] == 100){
                    echo "window.location.href='".__ROOT__."/index.php/Home/User/reg100';";
                }else{
                    echo "window.location.href='".__ROOT__."/index.php/Home/User/reg200';";
                }
                echo "</script>";
                exit;
            }
            $menber =M('menber');
            //  用户名
            $res_user =$menber->where(array('tel'=>$_POST['tel']))->select();
            if($res_user[0]){
                echo "<script>alert('用户电话已存在');";
                if((int)$_POST['num'] == 100){
                    echo "window.location.href='".__ROOT__."/index.php/Home/User/reg100';";
                }else{
                    echo "window.location.href='".__ROOT__."/index.php/Home/User/reg200';";
                }
                echo "</script>";
                exit;
            }
            //  金额
            $res_menber =$menber->where(array('uid'=>session('uid')))->select();
            $chargebag = bcsub($res_menber[0]['chargebag'],$_POST['num'],2);
            if($res_menber[0]['chargebag'] < $_POST['num']){
                echo "<script>alert('积分不足');";
                if((int)$_POST['num'] == 100){
                    echo "window.location.href='".__ROOT__."/index.php/Home/User/reg100';";
                }else{
                    echo "window.location.href='".__ROOT__."/index.php/Home/User/reg200';";
                }
                echo "</script>";
                exit;
            }
            $menber->where(array('uid'=>session('uid')))->save(array('chargebag'=>$chargebag));
            $data['name'] =$_POST['name'];
            $data['tel'] =$_POST['tel'];
            $data['pwd'] =$_POST['pwd'];
            $data['pwd2'] =$_POST['pwd2'];
            $data['type'] =1;
            $data['fuid'] =session('uid');
            $data['addtime'] =time();
            $data['addymd'] = date('Y-m-d',time());

            if($_POST['num'] ==100){
                $data['chargebag'] =100 ;
            }else{
                $data['chargebag'] =200 ;
            }

            $res =$menber->add($data);

            $income =M('incomelog');
            $data1['type'] =5;
            $data1['state'] =2;
            $data1['reson'] ='注册下级';
            $data1['addymd'] =date('Y-m-d',time());
            $data1['addtime'] =time();
            $data1['orderid'] =$res;
            $data1['userid'] =session('uid');
            $data1['income'] =$_POST['num'];
            if($_POST['num'] > 0){
                $income->add($data1);
            }

            if($res){
                //更新 uids
                if($res_menber[0]['fuids']){
                    $fuids = $menber->where(array('uid'=>session('uid')))->find();
//                    if($fuids['fuid']){
//
//                    }else{
//                        $fuids =session('uid').",";
//                    }
                    $fuids = $fuids['fuids'].$res.",";
                    $menber->where(array('uid'=>$res))->save(array('fuids'=>$fuids));
                }


                // 上家金额记录
//                $datas['state'] = 2;
//                $datas['reson'] = "注册下级";
//                $datas['type'] = 5;
//                $datas['addymd'] = date('Y-m-d',time());
//                $datas['addtime'] = date('Y-m-d H:i:s',time());
//                $datas['orderid'] = $res;
//                $datas['userid'] = session('uid');
//                $datas['income'] = $_POST['radio1'];
//                $this->savelog($datas);
                //下家金额记录
                $data1['state'] = 1;
                $data1['reson'] = "注册收入";
                $data1['type'] = 1;
                if($_POST['num'] ==100){
                    $nums = 1;
                }else{
                    $nums =2;
                }

                $data1['addymd'] = date('Y-m-d',time());
                $data1['addtime'] = time();
                $data1['orderid'] = session('uid');     // 注册上家
                $data1['userid'] =$res;
                $data1['income'] = $nums;
                $this->savelog($data1);
            }
            echo "<script>alert('用户".$_POST['name']."注册成功');";
            if((int)$_POST['num'] == 100){
                echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
            }else{
                echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
            }
            echo "</script>";
            exit;

        }

        $this->display();
    }

    public function recharge(){
        $this->display();
    }

    /**
     * 支付宝支付post提交页面
     */
    public function alipay(){
        if (IS_POST){
            Vendor('Alipay.aop.AopClient');
            Vendor('Alipay.aop.request.AlipayTradeWapPayRequest');
            //$out_trade_no = I('post.order_sn');
            /*
             *  $out_trade_no 为自己业务逻辑中要支付的订单号
             *      可从POST数据中提取，具体安全起见可自行加密操作 此处仅举例测试数据
             *  $order_amount 为要进行支付的金额 注意要用小数转换
             *      例如：3.50，10.00
             *  $aliConfig 获取支付宝配置数据
             */
            $out_trade_no = '2017M'.time();
            $body = '欢迎购买商品，愿您购物愉快';
            $subject = '你好';
            $order_amount = 9.00;
            $aliConfig = C('ALI_CONFIG');
            $aop = new \AopClient();
            $aop->gatewayUrl = $aliConfig['gatewayUrl'];
            $aop->appId = $aliConfig['appId'];
            $aop->rsaPrivateKey = $aliConfig['rsaPrivateKey'];
            $aop->alipayrsaPublicKey=$aliConfig['alipayrsaPublicKey'];
            $aop->apiVersion = '1.0';
            $aop->postCharset='UTF-8';
            $aop->format='json';
            $aop->signType='RSA2';
            $request = new \AlipayTradeWapPayRequest ();
            $bizContent = "{" .
                "    \"body\":\"$body.\"," .
                "    \"subject\":\"$subject\"," .
                "    \"out_trade_no\":\"$out_trade_no\"," .
                "    \"timeout_express\":\"90m\"," .
                "    \"total_amount\":$order_amount," .
                "    \"product_code\":\"QUICK_WAP_WAY\"" .
                "  }";
            $request->setBizContent($bizContent);
            $request->setNotifyUrl($aliConfig['notifyUrl']);
            $request->setReturnUrl($aliConfig['returnUrl']);
            $result = $aop->pageExecute ( $request);
            echo $result;
        }else{
            echo 'sorry,非法请求失败';
        }
    }

    /*
     * 积分充值
     */
    public function rechargedo(){
        $money = $_GET['money'];
        date_default_timezone_set('Asia/Shanghai');
        header("Content-type: text/html; charset=utf-8");
        $pay_memberid = "10071";   //商户ID
        $pay_orderid = date("YmdHis").rand(1000,9999);    //订单号
        $pay_amount = $money;    //交易金额
        $pay_applydate = date("Y-m-d H:i:s");  //订单时间
        $pay_bankcode = "WXZF";   //银行编码
        $uid = session('uid');

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


        $pay_notifyurl = "http://www.898tj.com/index.php/Home/Login/pay/token/admin123/id/$logid";   //服务端返回地址
        $pay_callbackurl = "http://www.898tj.com/index.php/Home/Login/login";  //页面跳转返回地址
        $Md5key = "4ql4b2k6y534476d3xjztd9t3k8avc";   //密钥
        $tjurl = "http://www.zhizhufu.com.cn/Pay_Index.html";   //网关提交地址
        $jsapi = array(
            "pay_memberid" => $pay_memberid,
            "pay_orderid" => $pay_orderid,
            "pay_amount" => $pay_amount,
            "pay_applydate" => $pay_applydate,
            "pay_bankcode" => $pay_bankcode,
            "pay_notifyurl" => $pay_notifyurl,
            "pay_callbackurl" => $pay_callbackurl,
        );

        ksort($jsapi);
        $md5str = "";
        foreach ($jsapi as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
//echo($md5str . "key=" . $Md5key."<br>");
        $sign = strtoupper(md5($md5str . "key=" . $Md5key));
        $jsapi["pay_md5sign"] = $sign;
        $jsapi["pay_tongdao"] = 'Ucwxscan'; //通道
        $jsapi["pay_tradetype"] = 900021; //通道类型   900021 微信支付，900022 支付宝支付
        $jsapi["pay_productname"] = '会员服务'; //商品名称
// print_r($jsapi);die;
        $data=http_build_query($jsapi);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $data,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($tjurl, false, $context);
        $result =json_decode($result);

        $this->assign("img",$result->code_img_url);

        $this->display();
    }


    /*
    * 完善资料
     */
    public function complete(){
        if($_POST['pwd'] && $_POST['pwd2'] ){
            $data = $_POST;
            M("menber")->where(array('uid'=>session('uid')))->save($data);
            echo "<script>";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/complete';";
            echo "</script>";
            exit;
        }
        $this->display();
    }

    public function rechargeDetail(){
        $incomelog =M('incomelog');
        $con['userid'] = session('uid');
        $con['type']   =2;
        $res = $incomelog->where($con)->order(" id DESC ")->limit(18)->select();
        $this->assign('res',$res);
        $this->display();
    }


    public function drawDetail(){
        $incomelog =M('incomelog');
        $con['userid'] = session('uid');
        $con['type']   =7;
        $res = $incomelog->where($con)->order(" id DESC ")->limit(18)->select();
        $this->assign('res',$res);
        $this->display();
    }

    /*
    * z账单 1分红收益2充值 3静态提现  4升级  5 注册下级 6下单购买 7积分体现 8话费充值 9 回馈奖 10积分商城购买
     */
    public function funds(){
        $incomelog =M('incomelog');
        $con['userid'] = session('uid');
        $con['type']   =array('in',array(1,2,4,5,6,7,8,9,10));
        $con['state']   =array('in',array(1,2));
        $res = $incomelog->where($con)->order(" id DESC ")->limit(18)->select();
        $this->assign('res',$res);
        $this->display();
    }


    /**
     * @return int 1大于  0小于 没有到上限
     * 每日收益上限
     */
    public function isshang($uid){
        // 查询今日收益上线
        $todayincomeall = M("incomelog")->where(array('userid'=>$uid,'state'=>1,'addymd'=>date('Y-m-d',time())))->sum('income');
        $config= M("Config")->where(array('id'=>13))->find();
        if($todayincomeall > $config['value']){
            return 1;
        }else{
            return 0;
        }
    }

    /*
    * 动态  1收益 2充值 3静态提现  4动态体现  5 注册下级 6下单购买 7积分体现 8积分转账 9复投码转账 10静态收益 11 动态收益
     */
    public function sy_dong(){
        $incomelog =M('incomelog');
        $con['userid'] = session('uid');
//        $con['type']  =array('in',array(4,9,11));
        $con['type']   =11;

        $res = $incomelog->where($con)->order(" id DESC ")->limit(18)->select();
        $this->assign('res',$res);
        $this->display();
    }
    
    public function futou(){

        if($_POST['num'] > 0){
            if(!is_numeric($_POST['num'])){
                echo "<script>alert('请不要输入非法字符');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
                echo "</script>";
                exit;
            }
            $menber = M("menber");
            $userinfo = $menber->where(array('uid'=>session('uid')))->select();
            $needmoney =bcmul($_POST['num'],100);


            if($userinfo[0]['mif'] < $_POST['num']){
                echo "<script>alert('复投码不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
                echo "</script>";
                exit;
            }

            $userallmoney =$userinfo[0]['chargebag'];
            if($userallmoney < $needmoney){
                echo "<script>alert('积分不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
                echo "</script>";
                exit;
            }else{
               $left = bcsub($userinfo[0]['chargebag'] , $needmoney,2);
               $mif = bcsub ($userinfo[0]['mif'] , $_POST['num']);

               if($left >= 0 ){
                   $menber->where(array('uid'=>session('uid')))->save(array('chargebag'=>$left,'mif'=>$mif));
               }else{
                   echo "<script>alert('积分不足');";
                   echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
                   echo "</script>";
                   exit;
               }
                // 增加
                $mif = $userinfo[0]['dongbag'] + $_POST['num'];
                $menber->where(array('uid'=>session('uid')))->save(array('dongbag'=>$mif));
                $income =M('incomelog');
                $data['type'] =6;
                $data['state'] =2;
                $data['reson'] ='复投';
                $data['addymd'] =date('Y-m-d',time());
                $data['addtime'] =time();
                $data['orderid'] =session('uid');
                $data['userid'] =session('uid');
                $data['income'] =$needmoney;
                if($needmoney > 0){
                    $income->add($data);
                }


                $order['userid'] =session('uid');
                $order['productid'] =1 ;
                $order['productname'] ="复投码";
                $order['productmoney'] = 0;
                $order['states'] = 1;
                $order['orderid'] = $_POST['num'];
                $order['addtime'] = time();
                $order['addymd'] = date("Y-m-d",time());
                $order['num'] = $_POST['num'];
                $order['prices'] =$needmoney;
                $order['totals'] =$needmoney;
                if($_POST['num'] > 0){
                    M("orderlog")->add($order);
                }

                echo "<script>alert('购买成功');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
                echo "</script>";
                exit;

            }

        }
        $myorder = M("orderlog")->where(array('userid'=>session('uid'),'type'=>1))->select();
        $count = 0;
        foreach ($myorder as $v){
            if($v['num']){
                $count = $count + $v['num'];
            }
        }
        $this->assign('count',$count);
//        $this->assign('config',$config[0]);
        $this->display();
    }

    private function getflilv($count){
        $configboj =M('config');
        if($count > 1 && $count < 4){   // 1

           $lilv =  $configboj->where(array('id'=>3))->select();
           return $lilv[0]['value'];

        }elseif ($count >3 && $count < 8){  // 2

            $lilv =  $configboj->where(array('id'=>4))->select();
            return $lilv[0]['value'];

        }elseif ($count >7 && $count < 12){   // 3

            $lilv =  $configboj->where(array('id'=>5))->select();
            return $lilv[0]['value'];

        }elseif ($count >11 && $count < 16){   // 4

            $lilv =  $configboj->where(array('id'=>6))->select();
            return $lilv[0]['value'];

        }elseif ($count >15 && $count < 20){   // 5

            $lilv =  $configboj->where(array('id'=>7))->select();
            return $lilv[0]['value'];

        }elseif ($count >20 && $count < 22){   // 6

            $lilv =  $configboj->where(array('id'=>8))->select();
            return $lilv[0]['value'];
        }else{
            return 0 ;
        }
    }


    public function isTiXian($userid,$num){
        $config = M('config');
        // 是否最大金额
        $nummax = $config->where(array('id'=>15))->select();
        if($num < $nummax[0]['value']){
            return "最低提现金额为".$nummax[0]['value'];
        }

        // 最大次数
        $timemax = $config->where(array('id'=>16))->select();
        $nowday = date("Y-m-d",time());
        $cond['addymd'] = $nowday;
        $cond['userid'] = $userid;
        $cond['type'] = array('in',array(3,4));
        $times = M('incomelog')->where($cond)->select();
        $last = count($times);
        if($last > $timemax[0]['value']){
            return "最大提次数为".$timemax[0]['value'];
        }else{
            return '';
        }



    }
    /*
    * 我的团队
     */
    public function my_group(){
        $uid =session('uid');
        $str = $this->get_category($uid,1);
        $newstr = substr($str,0,strlen($str)-1);
        $nextuser = M("menber")->where(array('uid'=>array('in',$newstr)))->select();
        if($nextuser[0]){
            foreach ($nextuser as $key=>$value){
                if($value['uid'] ==$uid){
                    continue;
                }
                if($value['fuids']){
                    $newstrs = substr($value['fuids'],0,strlen($value['fuids'])-1);
                    $array =array_reverse(explode(',',$newstrs));
                    foreach ($array as $k1=>$v1){
                        if($v1 == $uid){
                            $temp[$k1][]  =$value;
                        }
                    }
                }

            }
        }

        $this->assign('res',$temp);
        $this->display();
    }

    private function changeTimes($times){
        if($times ==1 ){
            return "一";
        }elseif ($times ==2 ){
            return "二";
        }elseif ($times == 3){
            return "三";
        }elseif ($times == 4 ){
            return "四";
        }elseif ($times ==5 ){
            return "五";
        }elseif ($times ==6 ){
            return "六";
        }
    }

    function getMenuTree($arrCat, $parent_id = 0, $level = 0)
    {
        static  $arrTree = array(); //使用static代替global
        if( empty($arrCat)) return FALSE;
        $level++;
        foreach($arrCat as $key => $value)
        {
            if($value['parent_id' ] == $parent_id)
            {
                $value[ 'level'] = $level;
                $arrTree[] = $value;
                unset($arrCat[$key]); //注销当前节点数据，减少已无用的遍历
                getMenuTree($arrCat, $value[ 'id'], $level);
            }
        }

        return $arrTree;
    }


    /**
     *  复投互转
     */
    public function transfer_futou()
    {
        if($_POST){
            if($_POST['num']<=0){
                echo "<script>alert('金额不正确');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_futou';";
                echo "</script>";
                exit;
            }

            $menber =M('menber');
            $res_user = $menber->where(array('uid'=>session('uid')))->select();
            if($res_user[0]['pwd2']!=$_POST['pwd2']){
                echo "<script>alert('二级密码不正确');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_futou';";
                echo "</script>";
                exit;
            }
            $res_user1 = $menber->where(array('tel'=>$_POST['tel']))->select();
            if($res_user1[0]['name'] != $_POST['name']){
                echo "<script>alert('账户不正确');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_futou';";
                echo "</script>";
                exit;
            }
            if($res_user[0]['mif']<$_POST['num']){
                echo "<script>alert('余额不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_futou';";
                echo "</script>";
                exit;
            }
            if($res_user[0]['tel']==$_POST['tel']){
                echo "<script>alert('自己不能给自己转账');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_futou';";
                echo "</script>";
                exit;
            }

            //处理自己
            $chargebagmy =bcsub($res_user[0]['mif'],$_POST['num'],2);
            $menber->where(array('uid'=>session('uid')))->save(array('mif'=>$chargebagmy));
            $income =M('incomelog');
            $logdata['type'] = 9 ;
            $logdata['state'] =2 ;
            $logdata['reson'] ='复投码转账' ;
            $logdata['addymd'] =date('Y-m-d',time()) ;
            $logdata['addtime'] =time() ;
            $logdata['orderid'] =$res_user1[0]['uid'] ;
            $logdata['userid'] =session('uid');
            $logdata['income'] =$_POST['num'];
            $income->add($logdata);

            //处理他人
            $chargebaghim =bcadd($res_user1[0]['mif'],$_POST['num'],2);
            $menber->where(array('uid'=>$res_user1[0]['uid']))->save(array('mif'=>$chargebaghim));

            $logdata['type'] = 9;
            $logdata['state'] =1 ;
            $logdata['reson'] ='复投码转账' ;
            $logdata['addymd'] =date('Y-m-d',time()) ;
            $logdata['addtime'] =time();
            $logdata['orderid'] =session('uid');
            $logdata['userid'] =$res_user1[0]['uid'];
            $logdata['income'] =$_POST['num'];
            $income->add($logdata);
            echo "<script>alert('转账成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
            echo "</script>";
            exit;
        }
        $this->display();
    }

    /**
     *  积分互转 1收益 2充值 3静态提现  4动态体现  5 注册下级 6下单购买 7积分体现 8积分转账 9复投码转账 10静态收益 11 动态收益
     */
    public function transfer_jifen()
    {
        $lilv =  M("config")->where(array('id'=>19))->find();
        $lilv =$lilv['value'];
        if($_POST){
            if($_POST['num']<=0){
                echo "<script>alert('金额不正确');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_jifen';";
                echo "</script>";
                exit;
            }

            if($_POST['num']% 10 != 0){
                echo "<script>alert('请输入10的整数倍');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_jifen';";
                echo "</script>";
                exit;
            }

            $menber =M('menber');
            $res_user = $menber->where(array('uid'=>session('uid')))->select();
            if($res_user[0]['pwd2']!=$_POST['pwd2']){
                echo "<script>alert('二级密码不正确');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_jifen';";
                echo "</script>";
                exit;
            }
            $res_user1 = $menber->where(array('tel'=>$_POST['tel']))->select();
            if($res_user1[0]['name'] != $_POST['name']){
                echo "<script>alert('账户名不正确');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_jifen';";
                echo "</script>";
                exit;
            }



            $fei =bcmul($_POST['num'],$lilv,2);
            $left =bcsub($res_user[0]['chargebag'],$fei,2);

            if($left<$_POST['num']){
                echo "<script>alert('积分不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_jifen';";
                echo "</script>";
                exit;
            }
            if($res_user[0]['tel']==$_POST['tel']){
                echo "<script>alert('自己不能给自己转账');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/transfer_jifen';";
                echo "</script>";
                exit;
            }

            //处理自己
            $chargebagmy =bcsub($left,$_POST['num'],2);
            $menber->where(array('uid'=>session('uid')))->save(array('chargebag'=>$chargebagmy));
            $income =M('incomelog');
            $logdata['type'] = 8 ;
            $logdata['state'] =2 ;
            $logdata['reson'] ='积分转账' ;
            $logdata['addymd'] =date('Y-m-d',time()) ;
            $logdata['addtime'] =time();
            $logdata['orderid'] =$res_user1[0]['uid'] ;
            $logdata['userid'] =session('uid');
            $logdata['income'] =bcadd($_POST['num'],$fei,2);
            $income->add($logdata);
            //处理他人
            $chargebaghim =bcadd($res_user1[0]['chargebag'],$_POST['num'],2);
            $menber->where(array('uid'=>$res_user1[0]['uid']))->save(array('chargebag'=>$chargebaghim));

            $logdata['type'] =8 ;
            $logdata['state'] =1 ;
            $logdata['reson'] ='积分转账' ;
            $logdata['addymd'] =date('Y-m-d',time()) ;
            $logdata['addtime'] =time() ;
            $logdata['orderid'] =session('uid');
            $logdata['userid'] =$res_user1[0]['uid'];
            $logdata['income'] =$_POST['num'];
            $income->add($logdata);
            echo "<script>alert('转账成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
            echo "</script>";
            exit;
        }
        $this->assign('lilv',$lilv);
        $this->display();
    }


    private function savelog($data){
        $incomelog =M('incomelog');
        return $incomelog->add($data);
    }


    public function payRecord(){  //充值记录
        $incomelog =M('incomelog');
        $condtion['userid'] =session('uid');
        $condtion['type']=2;
        $condtion['state']=1;
        $res = $incomelog->order('id DESC')->where($condtion)->select();
        $this->assign('res',$res);
        $this->display();
    }

    public function cancel(){
        $incomelog =M('incomelog');
        $condtion['uid'] =session('uid');
        $condtion['id']  =$_GET['id'];
        $res = $incomelog->where($condtion)->select();
        $income =$res[0]['income'];
        if($income<=0){
            echo "<script>alert('取消失败');";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/cashRecord';";
            echo "</script>";
            exit;
        }
        $menber =M('menber');
        $useinfo = $menber->where(array('uid'=>session('uid')))->select();
//        $res_usermoney = $useinfo[0]['incomebag']+$income;
        $res_usermoney = bcadd($useinfo[0]['incomebag'],$income,2);
        $menber->where(array('uid'=>session('uid')))->save(array('incomebag'=>$res_usermoney));
        $incomelog->where(array('id'=>$_GET['id']))->save(array('state'=>3));
        echo "<script>alert('操作成功');";
        echo "window.location.href='".__ROOT__."/index.php/Home/User/cashRecord';";
        echo "</script>";
        exit;
    }

    public function cashRecord(){  //提现记录
        $incomelog =M('incomelog');
        $condtion['userid'] =session('uid');
        $condtion['type']=3;
//        $condtion['state']=2;
        $res = $incomelog->order('id DESC')->where($condtion)->select();
        $this->assign('res',$res);
        $this->display();
    }

    public function cashDetail(){  //资金明细
        $incomelog =M('incomelog');
        $condtion['userid'] =session('uid');
        $condtion['type']   =array('gt',0);
        $res = $incomelog->order('id DESC')->where($condtion)->select();
        $this->assign('res',$res);
        $this->display();
    }

    public function switchMoney(){  //钱包互转
        if($_POST['chargebag']){  // 处理充值钱包转入到收益钱包
            if($_POST['chargebag']<=0){
                echo "<script>alert('请输入正确金额');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/switchMoney';";
                echo "</script>";
                exit;
            }
            // 处理充值钱包转入到收益钱包
            $menber =M('menber');
            $useinfo =$menber->where(array('uid'=>session('uid')))->select();
            if($useinfo[0]['chargebag']>$_POST['chargebag']){
//                $chargebag =$useinfo[0]['chargebag']-$_POST['chargebag'];
                $chargebag =bcsub($useinfo[0]['chargebag'],$_POST['chargebag'],2);
                $data['chargebag']=$chargebag;
//                $incomebag =$useinfo[0]['incomebag']+$_POST['chargebag'];
                $incomebag =bcadd($useinfo[0]['incomebag'],$_POST['chargebag'],2);
                $data['incomebag']=$incomebag;
                $menber->where(array('uid'=>session('uid')))->save($data);
                echo "<script>alert('转入成功');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
                echo "</script>";
                exit;
            }else{
                echo "<script>alert('账户余额不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/switchMoney';";
                echo "</script>";
                exit;
            }
        }
        //收益钱包转入到充值钱包
        if($_POST['incomebag']){
            if($_POST['incomebag']<=0){
                echo "<script>alert('请输入正确金额');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/switchMoney';";
                echo "</script>";
                exit;
            }
            // 处理充值钱包转入到收益钱包
            $menber =M('menber');
            $useinfo =$menber->where(array('uid'=>session('uid')))->select();
            if($useinfo[0]['incomebag']>$_POST['incomebag']){
//                $chargebag =$useinfo[0]['chargebag']+$_POST['incomebag'];
                $chargebag =bcadd($useinfo[0]['chargebag'],$_POST['incomebag'],2);
                $data['chargebag']=$chargebag;
//                $incomebag =$useinfo[0]['incomebag']-$_POST['incomebag'];
                $incomebag =bcsub($useinfo[0]['incomebag'],$_POST['incomebag'],2);
                $data['incomebag']=$incomebag;
                $menber->where(array('uid'=>session('uid')))->save($data);
                echo "<script>alert('转入成功');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
                echo "</script>";
                exit;
            }else{
                echo "<script>alert('账户余额不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/switchMoney';";
                echo "</script>";
                exit;
            }
        }
        $this->display();
    }

    public function transfer(){
        $type = $_GET['type'];
        if($type ==1){
            $title = "动态转账";
            $action = "transfers_dong";
        }else{
            $title = "静态转账";
            $action = "transfers_jing";
            $type  = 2;
        }
        $this->assign('title',$title);
        $this->assign('type',$type);
        $this->assign('action',$action);
        $this->display();
    }

    public function transferto(){
        $type = $_GET['type'];
        $menber =M('menber');
        if($_POST['num'] > 0 ){
            $userinfo =$menber->where(array('uid'=>session('uid')))->select();
            if($_POST['pwd2'] != $userinfo[0]['pwd2']){
                echo "<script>alert('二级密码错误');";
                echo "</script>";
                $this->display();
                exit();
            }

            if($type ==1 ){   // 动态钱包
                if($_POST['num'] > $userinfo[0]['dongbag']){
                    echo "<script>alert('动态钱包余额不足');";
                    echo "</script>";
                    $this->display();
                    exit();
                }

                $left =bcsub($userinfo[0]['dongbag'] ,$_POST['num'],2);
                $menber->where(array('uid'=>session('uid')))->save(array('dongbag'=>$left));

            }else{
                if($_POST['num'] > $userinfo[0]['jingbag']){
                    echo "<script>alert('静态钱包余额不足');";
                    echo "</script>";
                    $this->display();
                    exit();
                }
                $left =bcsub($userinfo[0]['jingbag'] ,$_POST['num'],2);
                $menber->where(array('uid'=>session('uid')))->save(array('jingbag'=>$left));


            }

            $dongbug = bcadd($userinfo[0]['chargebag'] ,$_POST['num'],2);
            $menber->where(array('uid'=>session('uid')))->save(array('chargebag'=>$dongbug));
            echo "<script>alert('转入成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
            echo "</script>";
            exit;
        }
        if($type ==1){
            $title = "动态钱包 转 充值钱包";

        }else{
            $title = "静态钱包 转 充值钱包";
            $type  = 2;
        }
        $this->assign('title',$title);
        $this->assign('type',$type);
        $this->display();
    }

    public function touch(){
        $type = isset($_GET['type']) ? $_GET['type'] : 1 ;
        if($type==1){
            $filename = "kefu.jpg";
            $msg = "联系客服";
        }else{
            $msg = "联系客服";
            $filename = "kefu.jpg";
        }
        $this->assign('msg',$msg);
        $this->assign('filename',$filename);
        $this->display();
    }

    public function inputnum(){
        if($_POST['num'] > 0){
            echo "<script>";
            echo "window.location.href='".__ROOT__."/index.php/Home/Pay/getQrCode/money/".$_POST['num']."';";
            echo "</script>";
            exit;
        }
        $this->display();
    }

    public function inputzhifu(){
        echo "<script>alert('支付宝暂未开通');";
        echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
        echo "</script>";
        exit;

        if($_POST['num'] > 0){
            echo "<script>";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/recharge/money/".$_POST['num']."';";
            echo "</script>";
            exit;
        }
        $this->display();
    }
}