<?php

namespace Admin\Controller;

use Think\Controller;

class UserController extends Controller
{

    public function qrcode()
    {
        Vendor('phpqrcode.phpqrcode');
        $id = I('get.id');
        //生成二维码图片 http://localhost/index.php/Home/Login/reg
        $object = new \QRcode();
        $url = "http://" . $_SERVER['HTTP_HOST'] . '/index.php/Home/Login/reg/fid/' . $id;

        $level = 3;
        $size = 5;
        $errorCorrectionLevel = intval($level);//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    public function login()
    {
        if (IS_POST) {
            $name = I('post.name');
            $pwd = I('post.pwd');
            $user = M('user');
            if (!$name || !$pwd) {
                echo "<script>alert('用户名或密码不存在');";
                echo "window.history.go(-1);";
                echo "</script>";
            }
            $result = $user->where(array('name' => $name))->select();
            if ($result[0]['password'] == $pwd) {
                $_SESSION['uname'] = $name;
                echo "<script>window.location.href = '" . __ROOT__ . "/index.php/Admin/Index/main';</script>";
            } else {
                $_SESSION['number'] = $_SESSION['number'] + 1;
                if ($_SESSION['number'] > 2) {
                    $user->where(array('name' => $name))->save(array('manager' => 0));
                }
                echo "<script>alert('用户名或密码不存在,');";
                echo "</script>";
                $this->display();
            }
        }
        $this->display();
    }

    public function logOut()
    {
        session('uname', null);
        cookie('is_login', null);
        echo "<script>window.location.href = '" . __ROOT__ . "/index.php/Admin/User/login';</script>";
    }

    /**
     * 静态收益 ok
     * 1收益 2充值 3静态提现  4动态体现  5 注册下级 6下单购买 7积分体现 8积分转账 9复投码转账 10分红收益 11 动态收益
     */
    public function crontab()
    {  //我的团队
        $incomelog = M('incomelog');
//        $res = $incomelog->where(array('addymd'=>date('Y-m-d'),'type'=>10))->select();
//
//        if($res[0]){
//            print_r('今日受益已结算');die;
//        }
        $str ='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        $menber = M("menber");
        if($this->isdong() == 2){
            echo $str;
            echo "<script>alert('资金未处于冻结状态');";
            echo "window.location.href='".__ROOT__."/index.php/Admin/Config/index';";
            echo "</script>";
            exit;
        }
        $alluser = $menber->select();
        foreach ($alluser as $key => $val) {
            // 收益
              if($val['djbag'] > 0){
                  $data['state'] = 1;
                  $data['reson'] = "产生利息";
                  $data['type'] = 1;
                  $data['addymd'] = date('Y-m-d', time());
                  $data['addtime'] = time();
                  $data['orderid'] = 0;
                  $data['userid'] = $val['uid'];
                  $incomes = bcmul($val['djbag'],0.001,2);
                  if($incomes > 0){
                      $data['income'] = $incomes;
                      $this->savelog($data);
                      $this->addmoney($val['uid'],$incomes);
                  }

              }
//            $this->addmoney($val['uid'], $incomes);
//            $this->savelog($data);

        }

        echo 'success';
    }



    private function isdong(){
        $config =M("config")->where(array('id'=>3))->find();
        if($config['value'] ==1){
            return 1;
        }else{
            return 2;
        }
    }

    /**
     * 用户收入
     * @param $uid
     * @param $money
     */
    private function addmoney($uid, $money)
    {
        $menber = M("menber");
        $userinfos = $menber->where(array('uid' => $uid))->select();
        $dongbag = $afterincom = bcadd($userinfos[0]['djbag'], $money, 2);
        $menber->where(array('uid' => $uid))->save(array('djbag'=>$dongbag));
    }

    /**
     * @param $uid
     * @param $type
     * @param $out
     * @return int
     * 每个类型的牛 收益是否到期
     */
    public function isincome($uid, $type, $out)
    {
        $daycomelogs = M('incomelog')->where(array('type' => $type, 'userid' => $uid))->sum('income');
        if ($daycomelogs >= $out) {
            return 1;
        }
        return 0;
    }

    /**
     * @return int 1大于  0小于 没有到上限
     * 每日收益上限
     */
    public function isshang($uid)
    {
        // 查询今日收益上线
        $todayincomeall = M("incomelog")->where(array('userid' => $uid, 'state' => 1, 'addymd' => date('Y-m-d', time())))->sum('income');
        $config = M("Config")->where(array('id' => 13))->find();
        if ($todayincomeall >= $config[0]['value']) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @return int ok
     * 是否有每日收益
     */
    public function getusernums($userid)
    {
        $income = M('incomelog');
        $daycomelogs = $income->where(array('type' => 10, 'userid' => $userid))->sum('income');
        $conf = M("config")->where(array('id' => 1))->find();
        if ($daycomelogs >= $conf['value']) {
            return 1;
        } else {
            return 0;
        }
    }

    private function savelog($data)
    {
        $incomelog = M('incomelog');
        return $incomelog->add($data);
    }


    public function crantabUserIncome()
    {
        $menber = M('menber');
        $income = M('incomelog');
        if ($_GET['uid']) {
            $map['uid'] = $_GET['uid'];
        } else {
            $map['uid'] = array('gt', 9);
        }
        $result_user = $menber->where($map)->select();
        foreach ($result_user as $k => $v) {
            $chargebag = $v['chargebag'];
            $incomebag = $v['incomebag'];
            $allIncome = bcadd($chargebag, $incomebag, 2);  // 所有钱包

            $daycomelogs = $income->where(array('state' => 1, 'userid' => $v['uid']))->select();
            $userIncome = 0;
            foreach ($daycomelogs as $k1 => $v1) {         // 收益
                $userIncome = bcadd($userIncome, $v1['income'], 2);
            }
            if ($_GET['uid']) {
                print_r("每日收益==》" . $userIncome);
            }
            $dayoutlogs = $income->where(array('state' => 2, 'userid' => $v['uid']))->select();

            $userOut = 0;                              // 支出
            foreach ($dayoutlogs as $k2 => $v2) {
                $userOut = bcadd($userOut, $v2['income'], 2);
            }
            if ($_GET['uid']) {
                print_r("<br>总支出==》" . $userOut);
            }
            $allIncomesUser = bcsub($userIncome, $userOut, 2);      // 总收入
            if ($allIncomesUser < 0) {
                print_r("userID" . $v['uid'] . "收入日志异常");
            }
            $layout = $allIncomesUser - $allIncome;
            if ($layout != 0) {
                print_r("用户ID：" . $v['uid'] . "<br>");
                print_r("钱包总额：" . $allIncome . "<br>");
                print_r("收入总额：" . $allIncomesUser . "<br><br><br>");
            }
        }
//        print_r($result_user);die;
    }


    function crontabRite()
    {
        $today = date('m-d', time());
        $isdate = M("Rite")->where(array('date' => $today))->select();
        if ($isdate[0]) {
//            $config= M("Config")->where(array('name'=>'daily_income'))->select();
//            M("Rite")->where(array('date'=>$today))->save(array('cont'=>$config[0]['val'],'date'=>$today));
            echo 2;
            exit();
        } else {
            $config = M("Config")->where(array('id' => 1))->select();
            M("Rite")->add(array('cont' => $config[0]['value'], 'date' => $today));
            echo 1;
            exit();
        }
    }
}


?>