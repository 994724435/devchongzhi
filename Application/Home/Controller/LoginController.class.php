<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class LoginController extends Controller{
    public function login(){
        session('uid',0);
        if($_POST){
//            if($_POST['number']!=$_POST['numbers']){
////                header("Content-Type:text/html; charset=utf-8");
////                exit('验证码错误'.'[ <A HREF="javascript:history.back()">返 回</A> ]');
//
//                echo "<script>alert('验证码错误');</script>";
////                $this->display();
////                exit();
//                echo "<script>window.location.href='".__ROOT__."/index.php/Home/Login/login';</script>";
//            }
            $menber =M('menber');
            $res = $menber->where(array('name'=>$_POST['tel']))->select();
            if($res[0]['pwd']==$_POST['pwd']){
                session_start();
                session('name',$_POST['name']);
                session('uid',$res[0]['uid']);
                echo "<script>window.location.href='".__ROOT__."/index.php/Home/Index/index';</script>";
            }else{
                echo "<script>alert('用户名或密码错误');</script>";
            }
        }
        session_start();
        $numbers = rand(1000,9999);
        $this->assign('numbers',$numbers);
        $this->display();
    }

    public function reg(){
        session('uid',0);
        $menber = M('menber');
        if($_POST){
            if(!$_POST['tel'] || !$_POST['pwd'] || !$_POST['pwd2'] || !$_POST['yzm']){
                echo "<script>alert('请将信息填写完整');</script>";
                $this->display();
                exit();
            }

            if($_POST['tel']){
                $tel = $menber->where(array('tel'=>$_POST['tel']))->select();
                if($tel[0]){
                    echo "<script>alert('电话号码已注册');</script>";
                    $this->display();
                    exit();
                }
            }
            if($_POST['pwd']!=$_POST['pwd11']){
                echo "<script>alert('登录密码不一致');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Login/reg';";
                echo "</script>";
                exit;
            }

            if($_POST['pwd2']!=$_POST['pwd22']){
                echo "<script>alert('支付密码不一致');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Login/reg';";
                echo "</script>";
                exit;
            }

            // 验证码
            $msg = M("message")->where(array('tel'=>$_POST['tel'],'state'=>1))->find();
            if($msg['cont'] !== $_POST['yzm']){
                echo "<script>alert('验证码不正确');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Login/reg';";
                echo "</script>";
                exit;
            }
            $fid = $_GET['fid'];
            $data['name'] = $_POST['tel'];
            $data['pwd'] = $_POST['pwd'];
            $data['pwd2'] = $_POST['pwd2'];
            $data['tel'] = $_POST['tel'];
            $data['email'] = $_POST['email'];
            $data['addtime'] = time();
            $data['addymd'] = date('Y-m-d',time());
            $data['dongbag'] ='0';
            $data['jingbag'] = '0';
            $data['chargebag'] = '0';

            if($fid){
                $data['fuid'] = $fid;
                $fidUserinfo = $menber->where(array('uid'=>$fid))->select();
                if(!$fidUserinfo[0]){
                    echo "<script>alert('上级用户名不存在');</script>";
                    $this->display();
                    exit();
                }
                $fuids = $fidUserinfo[0]['fuids'];
                $data['two'] = $fidUserinfo[0]['fuid'];
                $data['three'] = $fidUserinfo[0]['two'];
                $data['four'] = $fidUserinfo[0]['three'];
            }

            $userid = $menber->add($data);
            session_start();
            session('name',$_POST['name']);
            session('uid',$userid);

            if($fuids){
                $fuid1['fuids'] = $fuids.$userid.',';
            }else{
                $fuid1['fuids'] = $userid.',';
            }
            $menber->where(array('uid'=>$userid))->save($fuid1);


            echo "<script>window.location.href='".__ROOT__."/index.php/Home/Index/index';</script>";
            exit();
        }
        if($_GET['fid']) {
            $fidUserinfo = $menber->where(array('uid' => $_GET['fid']))->find();
            $this->assign('fuid',$fidUserinfo['tel']);
        }
        $this->display();
    }


    /**
     * 1 正确 2 已发送 3 格式不正确 4,已经注册
     */
    public function sendTel(){
        $tel =trim($_REQUEST['tel']);
        if(!preg_match("/^1[34578]{1}\d{9}$/",$tel)){
            echo 3;
        }

        $istel =M('menber')->where(array('tel'=>$tel))->select();
        if($istel[0]){
            echo 4;
            exit();
        }

        $message = M('message');
        $ismessage = $message->where(array('tel'=>$tel,'state'=>1))->select();
        if($ismessage[0]){
            echo 2;
            exit();
        }

        $data['session'] =md5(time() . rand(1,1000000));
        $data['cont'] = rand(1000,9999);
        $data['time'] = time();
        $data['tel'] = $tel;
        $data['date'] = date('Y-m-d',time());
        $data['state'] = 1;
        $message->add($data);

        vendor('Ucpaas.Ucpaas','','.class.php');
        //初始化必填
        $options['accountsid']='bbfd7ced6526e2dcae086c23a29f9b3a';
        $options['token']='58ae7eb5ce61bc7af4a63349b334b45b';
        $ucpass = new \Ucpaas($options);
        $appId = "d5595fef119f46da9f07ea023beb0608";
        $to = $tel;
        $templateId = "240596";
        $param=$data['cont'] ;
        $resmsg =$ucpass->templateSMS($appId,$to,$templateId,$param);
        session('messageEid',$data['session']);
        echo 1;
    }

    /**
     * 1 正确 2 已发送 3 格式不正确 4已经注册
     */
    public function sendEmail(){
        $emial =trim($_REQUEST['email']);
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if (!preg_match( $pattern, $emial ) )
        {
            echo 3;
            exit();
        }

        $istel =M('menber')->where(array('email'=>$emial))->select();
        if($istel[0]){
            echo 4;
            exit();
        }

        $message = M('message');
        $ismessage = $message->where(array('email'=>$emial,'state'=>1))->select();
        if($ismessage[0]){
            echo 2;
            exit();
        }


        $data['session'] =md5(time() . rand(1,1000000));
        $data['cont'] = rand(1000,9999);
        $data['time'] = time();
        $data['email'] = $emial;
        $data['date'] = date('Y-m-d',time());
        $data['state'] = 1;
        $message->add($data);
        $content = "您好！您的邮箱验证码为".$data['cont'];
        sendMail($emial,"MIF验证码",$content);
        session('messageEid',$data['session']);
        echo 1;
    }

    public function forgetPwd(){
        $this->display();
    }

    public function pay(){
        $token = $_GET['token'];
        if($token == "admin123"){
            $logid = $_GET['id'];
            $order = M("incomelog");
            echo trim("SUCCESS");
            $res= $order->where(array('id'=>$logid))->select();
            if(!$res[0]['state']){
                $order->where(array('id'=>$logid))->save(array('type'=>2,'state'=>1));
                $menber = M('menber')->where(array('uid'=>$res[0]['userid']))->select();
                $charbag =bcadd($menber[0]['chargebag'],$res[0]['income'],2);
                M('menber')->where(array('uid'=>$res[0]['userid']))->save(array('chargebag'=>$charbag));
            }
        }
    }
}