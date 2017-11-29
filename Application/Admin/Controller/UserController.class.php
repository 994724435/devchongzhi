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
        $orderlog = M("orderlog");
        $menber = M("menber");
        $configobj = M('config')->where(array('id' => 2))->select();

        $config3 = M('config')->where(array('id' => 3))->find();
        $config4 = M('config')->where(array('id' => 4))->find();
        $config5 = M('config')->where(array('id' => 5))->find();

        $config6 = M('config')->where(array('id' => 6))->find();
        $config7 = M('config')->where(array('id' => 7))->find();
        $config8 = M('config')->where(array('id' => 8))->find();

        $config2 = $configobj[0]['value'];
        $alluser = $menber->select();
        foreach ($alluser as $key => $val) {
            // 处理牛的收益
            $allorders = $orderlog->where(array('userid' => $val['uid'], 'state' => 1, 'type' => array('GT',1)))->select();
                $data['state'] = 1;
                $data['addymd'] = date('Y-m-d', time());
                $data['addtime'] = time();
                $data['userid'] = $val['uid'];
            $allyou = $orderlog->where(array('userid' => $val['uid'], 'state' => 1, 'type' =>2))->select();
            //幼崽牦牛 1000     ok
            $incomeyou = 0 ;
             if ($allyou[0]) {
                 $data['type'] = 10;
                 $income = bcmul($config6['value'], $config3['value'], 2); // 配置 乘以 利率 每日的收益
                 $incomes = bcmul($income,count($allyou),2);
                 $data['income'] = $incomes;
                 $data['reson'] = "幼崽牦牛收益";
                 $out =bcmul(1600,count($allyou));  // 出局总数
                 if ($this->isincome($val['uid'], 11, $out) == 1) {
                     $orderlog->where(array('userid' => $val['uid'], 'state' => 1, 'type' =>2))->save(array('state' => 2));
                 } else {
                     $incomeyou = $incomes ;
                     $this->addmoney($val['uid'], $incomes);
                     $this->savelog($data);
                 }
             }

            // 成年牦牛  5000  12
            $incomecheng = 0 ;
            $allcheng = $orderlog->where(array('userid' => $val['uid'], 'state' => 1, 'type' =>3))->select();
            if ($allcheng[0]) {
                $data['type'] = 11;
                $income = bcmul($config7['value'], $config4['value'], 2); // 配置 乘以 利率
                $incomes = bcmul($income,count($allcheng),2);
                $data['income'] = $incomes;
                $data['reson'] = "黑牦牛收益";
                $out =bcmul(8000,count($allcheng));  // 出局总数
                if ($this->isincome($val['uid'], 12, $out) == 1) {
                    $orderlog->where(array('userid' => $val['uid'], 'state' => 1, 'type' =>3))->save(array('state' => 2));
                } else {
                    $incomecheng = $incomes ;
                    $this->addmoney($val['uid'], $incomes);
                    $this->savelog($data);
                }
            }

            //母牦牛 10000 13
            $incomemu = 0 ;
            $allmu = $orderlog->where(array('userid' => $val['uid'], 'state' => 1, 'type' =>4))->select();
            if ($allmu[0]) {
                $data['type'] = 12;
                $income = bcmul($config8['value'], $config5['value'], 2); // 配置 乘以 利率
                $incomes = bcmul($income,count($allmu),2);
                $data['income'] = $incomes;
                $data['reson'] = "母牦牛收益";
                $out =bcmul(16000,count($allmu));  // 出局总数
                if ($this->isincome($val['uid'], 12, $out) == 1) {
                    $orderlog->where(array('userid' => $val['uid'], 'state' => 1, 'type' =>4))->save(array('state' => 2));
                } else {
                    $incomemu = $incomes ;
                    $this->addmoney($val['uid'], $incomes);
                    $this->savelog($data);
                }
            }

            // 处理地的收益
            $incomedi = 0 ;
            $diorder = $orderlog->where(array('userid' => $val['uid'], 'state' => 1, 'type' => 1))->select();
            if($diorder[0]){
                // 查询是否有收益
                if ($this->getusernums($val['uid']) == 0) {
                    $data['state'] = 1;
                    $data['reson'] = "牧场收益";
                    $data['type'] = 10;
                    $data['addymd'] = date('Y-m-d', time());
                    $data['addtime'] = time();
                    $data['orderid'] = $val['dongbag'];
                    $data['userid'] = $val['uid'];
                    $data['income'] = $config2;
                    $todayincome = $config2;
                    if ($todayincome > 0) {
                        $incomedi = $todayincome;
                        $this->addmoney($val['uid'], $todayincome);
                        $this->savelog($data);
                    }
                }
            }


            // 处理会员回馈奖收益
            if ($val['fuids'] && $val['fuid']) {   // 处理上家
                $all1 = bcadd($incomeyou,$incomecheng,2);
                $all2 = bcadd($all1,$incomemu,2);
                $allimcomes  = bcadd($all2,$incomedi,2);

                $data['state'] = 1;
                $data['reson'] = "回馈奖";
                $data['type'] = 9;
                $data['addymd'] = date('Y-m-d', time());
                $data['addtime'] = time();
                $data['orderid'] = $val['uid'];

                // 一级
                if($val['fuid']){
                    $data['income'] =  bcmul($allimcomes,0.04,2);
                    $data['userid'] = $val['fuid'];
                    if($data['income'] > 0 && $this->isdi($val['fuid'])){
                        $data['username'] = "一级下线回馈奖";
                        $data['tel'] = $val['tel'];
                        $this->addniu($val['fuid'], $data['income']);
                        $this->savelog($data);
                    }

                }
                // 二级
                if($val['two']){
                    $data['income'] =  bcmul($allimcomes,0.03,2);
                    $data['userid'] = $val['two'];
                    $data['username'] = "二级下线回馈奖";
                    $data['tel'] = $val['tel'];
                    if($data['income'] > 0 && $this->isdi($val['two'])){
                        $this->addniu($val['two'], $data['income']);
                        $this->savelog($data);
                    }

                }
                // 三级
                if($val['three']){
                    $data['income'] =  bcmul($allimcomes,0.03,2);
                    $data['userid'] = $val['three'];
                    $data['username'] = "三级下线回馈奖";
                    $data['tel'] = $val['tel'];
                    if($data['income'] > 0 && $this->isdi($val['three'])){
                        $this->addniu($val['three'], $data['income']);
                        $this->savelog($data);
                    }

                }

                // 四级
                if($val['four']){
                    $data['income'] =  bcmul($allimcomes,0.01,2);
                    $data['userid'] = $val['four'];
                    $data['username'] = "四级下线回馈奖";
                    $data['tel'] = $val['tel'];
                    if($data['income'] > 0 && $this->isdi($val['four'])){
                        $this->addniu($val['four'], $data['income']);
                        $this->savelog($data);
                    }

                    // 五到八
                    $fiveinfo = M("menber")->where(array('uid'=>$val['four']))->find();
                    // 五
                    if($fiveinfo['fuid']){
                        $data['income'] =  bcmul($allimcomes,0.01,2);
                        $data['userid'] =$fiveinfo['fuid'];
                        $data['username'] = "五级下线回馈奖";
                        $data['tel'] = $val['tel'];
                        if($data['income'] > 0 && $this->isdi($fiveinfo['fuid'])){
                            $this->addniu($fiveinfo['fuid'], $data['income']);
                            $this->savelog($data);
                        }
                    }
                    // 六
                    if($fiveinfo['two']){
                        $data['income'] =  bcmul($allimcomes,0.01,2);
                        $data['userid'] =$fiveinfo['two'];
                        $data['username'] = "六级下线回馈奖";
                        $data['tel'] = $val['tel'];
                        if($data['income'] > 0 && $this->isdi($fiveinfo['two'])){
                            $this->addniu($fiveinfo['two'], $data['income']);
                            $this->savelog($data);
                        }

                    }
                    // 七
                    if($fiveinfo['three']){
                        $data['income'] =  bcmul($allimcomes,0.01,2);
                        $data['userid'] =$fiveinfo['three'];
                        $data['username'] = "七级下线回馈奖";
                        $data['tel'] = $val['tel'];
                        if($data['income'] > 0 && $this->isdi($fiveinfo['three'])){
                            $this->addniu($fiveinfo['three'], $data['income']);
                            $this->savelog($data);
                        }

                    }
                    // 八
                    if($fiveinfo['four']){
                        $data['income'] =  bcmul($allimcomes,0.01,2);
                        $data['userid'] =$fiveinfo['four'];
                        $data['username'] = "八级下线回馈奖";
                        $data['tel'] = $val['tel'];
                        if($data['income'] > 0 && $this->isdi($fiveinfo['four'])){
                            $this->addniu($fiveinfo['four'], $data['income']);
                            $this->savelog($data);
                        }
                    }
                }
            }

            //牛气奖发放
            if($val['niuqi']){
                $li = $this->getniuli($val['uid']);
                if($li){
                    $data['type'] =4;
                    $data['state'] =2;
                    $data['reson'] ='牛气奖';
                    $data['addymd'] =date('Y-m-d',time());
                    $data['addtime'] =time();
                    $data['orderid'] =0;
                    $incomes_niu =bcmul($val['niuqi'],$li,2);
                    $data['income'] =$incomes_niu;
                    $data['userid'] =$val['uid'];
                    $this->savelog($data);
                    $curluser = $menber->where(array('uid'=>$val['uid']))->find();
                    $niuqi =  bcsub($curluser['niuqi'],$incomes_niu,2);
                    $chargebag =bcadd($curluser['chargebag'],$incomes_niu,2);
                    $menber->where(array('uid'=>$val['uid']))->save(array('niuqi'=>$niuqi,'chargebag'=>$chargebag));
                }
            }
        }

        echo 'success';
    }

    private function getniuli($uid){
        $cout =M('menber')->where(array('fuid'=>$uid))->count();
        if($cout){
            if($cout < 5){
                return 0.05;
            }elseif ($cout > 4 && $cout < 10){
                return 0.1;
            }else{
                $return = 0.2;
                $cha =$cout - 10;
                $left =$cha*0.02 + 0.2;
                return $left;
            }

        }else{
            return 0;
        }
    }

    private function isdi($uid){
        $logs =M("orderlog")->where(array('userid'=>$uid,'type'=>1))->find();
        if($logs['logid']){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 牛气奖
     * @param $uid
     * @param $money
     */
    private function addniu($uid, $money)
    {
        $menber = M("menber");
        $userinfos = $menber->where(array('uid' => $uid))->select();
        $money =bcmul($money,1.5,2);
        $afterincom = bcadd($userinfos[0]['niuqi'], $money, 2);
        $menber->where(array('uid' => $uid))->save(array('niuqi' => $afterincom));
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
        $bashi =bcmul($money,0.8,2);
        $chargebag = bcadd($userinfos[0]['chargebag'], $bashi, 2);
        $twos =bcmul($money,0.2,2);
        $dongbag = $afterincom = bcadd($userinfos[0]['dongbag'], $twos, 2);
        $menber->where(array('uid' => $uid))->save(array('chargebag' => $chargebag,'dongbag'=>$dongbag));
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