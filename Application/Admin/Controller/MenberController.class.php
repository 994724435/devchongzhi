<?php
namespace Admin\Controller;
use Think\Controller;
class MenberController extends CommonController {
	public function select(){
        $menber = M('menber');
        if($_GET['name']){
            $map['tel']=array('like','%'.$_GET['name'].'%');
            $users= $menber->where($map)->select();
        }else{
            $users= $menber->select();
        }
        $this->assign('users',$users);
        $this->display();
    }

    public function editeUser(){
	    $uid =$_GET['id'];
        $menber = M('menber');
        if($_POST && $uid){
            $mima =$_POST['mima'];
            unset($_POST['mima']);
            if($mima != "asd888"){
                echo "<script>alert('请输入正确密码');window.location.href = '".__ROOT__."/index.php/Admin/Menber/select';</script>";exit();
            }
            $data =$_POST;
            $menber->where(array('uid'=>$uid))->save($data);
            echo "<script>alert('修改成功');window.location.href = '".__ROOT__."/index.php/Admin/Menber/select';</script>";exit();
        }
        $userinfo = $menber->where(array('uid'=>$uid))->select();
        $this->assign('res',$userinfo[0]);
        $this->display();
    }

    public function charge(){
        $menber = M('menber');
        $users= $menber->select();
        if($_POST['user']&&$_POST['num']){
            if($_POST['num']<=0){
                echo "<script>alert('请输入正确金额');window.location.href = '".__ROOT__."/index.php/Admin/Menber/charge';</script>";exit();
            }
            $user = $menber->where(array('tel'=>$_POST['user']))->select();
            if(!$user[0]){
                echo "<script>alert('用户不存在');window.location.href = '".__ROOT__."/index.php/Admin/Menber/charge';</script>";exit();
            }

            $chargebag= $user[0]['chargebag']+$_POST['num'];
            $res = $menber->where(array('tel'=>$_POST['user']))->save(array('chargebag'=>$chargebag));

            $datas['state'] = 1;
            $datas['reson'] = "充值";
            $datas['type'] = 2;
            $datas['addymd'] = date('Y-m-d',time());
            $datas['addtime'] = date('Y-m-d H:i:s',time());
            $datas['orderid'] = $_SESSION['uname'];
            $datas['userid'] = $user[0]['uid'];
            $datas['income'] = $_POST['num'];
            $comelog =M('incomelog');

            if($this->isshang($user[0]['uid'])){
                echo "<script>alert('今日收入已达上线');";
                echo "window.location.href = '".__ROOT__."/index.php/Admin/Menber/charge';";
                echo "</script>";
                exit;
            }

            $comelog->add($datas);
            if($res){
                $message =$user[0]['name'].'成功充值'.$_POST['num'].'元';
                echo "<script>alert('$message');";
                echo "window.location.href = '".__ROOT__."/index.php/Admin/Menber/charge';";
                echo "</script>";
                exit;
            }

        }
        $this->assign('users',$users);
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

    public function addUser(){
        if($_POST ){
            $menber = M('menber');
            if($_POST['fuid']){
                $fuids = $menber->where(array('uid'=>$_POST['fuid']))->select();
                if($fuids[0]){
                    $fids =$fuids[0]['fuids'];
                }else{
                    echo "<script>alert('上级用户不存在');window.location.href = '".__ROOT__."/index.php/Admin/Menber/addUser';</script>";
                    $this->display();
                    exit();
                }
            }

            $isuser= $menber->where(array('tel'=>$_POST['tel']))->select();
            if($isuser[0]){
                echo "<script>alert('电话已注册');window.location.href = '".__ROOT__."/index.php/Admin/Menber/addUser';</script>";
                $this->display();
                exit();
            }
            $data =$_POST;
            $data['chargebag'] = '0';
            $data['addtime'] = time();
            $data['addymd'] =date("Y-m-d H:i:s",time());
            if($_POST['fuid']){
                $data['two'] = $fuids[0]['fuid'];
                $data['three'] = $fuids[0]['two'];
                $data['four'] = $fuids[0]['three'];
            }
            $uid =  $menber->add($data);

            if($fids){
                $fuid1['fuids'] = $fids.$uid.',';
            }else{
                $fuid1['fuids'] = $uid.',';
            }

            $menber->where(array('uid'=>$uid))->save($fuid1);
            echo "<script>alert('添加成功');window.location.href = '".__ROOT__."/index.php/Admin/Menber/select';</script>";exit();
        }
        $this->display();
    }

    public function usermessage(){
        $incomelog = M('incomelog');
//        if($_GET['productid']){
////            $map['name']=array('like','%'.$_GET['name'].'%');
//            $map['productid'] =$_GET['productid'];
//        }
        if($_GET['uid']){
            $map['userid'] =$_GET['uid'];
        }
        if($_GET['type']){
            $map['type'] =$_GET['type'];
        }
        if($_GET['mindate']&&$_GET['maxdate']){
            $map['addymd'] =array(array('elt',$_GET['maxdate']),array('egt',$_GET['mindate']),'and');;
        }
        $users= $incomelog->order('type asc')->where($map)->select();

        $this->assign('users',$users);
        $this->display();
    }

    public function userupdate(){
        $menber = M('menber');
        if($_POST){
            $data['name'] =$_POST['name'];
            $data['address'] =$_POST['address'];
            $data['sex'] =$_POST['sex'];
            $data['ismanager'] =$_POST['ismanager'];
            $data['group'] =$_POST['group'];
            $result = $menber->where(array('id'=>$_GET['id']))->save($data);
            if($result){
                echo "<script>alert('更新成功');window.location.href = '".__ROOT__."/index.php/Admin/Menber/select';</script>";exit();
            }else{
                echo "<script>alert('更新失败');window.location.href = '".__ROOT__."/index.php/Admin/Menber/select';</script>";exit();
            }
        }
        $user= $menber->where(array('id'=>$_GET['id']))->select();
        $this->assign('user',$user[0]);
        $this->display();
    }

//    删除用户
    public function userdelete(){
        $menber = M('menber');
        $result = $menber->where(array('id'=>$_GET['id']))->delete();
        if($result){
            echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Menber/select';</script>";exit();
        }else{
            echo "<script>alert('更新失败');window.location.href = '".__ROOT__."/index.php/Admin/Menber/select';</script>";exit();
        }
    }

    //充值审核
    public function chargesheng(){
        $income =M('incomelog');
        $data['p_incomelog.type'] =0;
        $data['p_incomelog.state'] =0;
        $data['p_incomelog.reson'] ='充值';
        $data['p_incomelog.addtime'] =array('gt',0);
        $result =$income->field('p_incomelog.addtime as addtimes,p_incomelog.addymd as addymds,p_menber.name,p_incomelog.userid,income,id')->join('p_menber ON p_incomelog.userid=p_menber.uid')->where($data)->select();
        $this->assign('res',$result);
        $this->display();
    }

    public function ischarge(){
        $income =M('incomelog');
        $result = $income->where(array('id'=>$_GET['id']))->select();
        if($result[0]){
            if($_GET['state']==1){
                $data['type'] =2;
                $data['state'] =1;
                $income->where(array('id'=>$_GET['id']))->save($data);
                $menber =M('menber');
                $user= $menber->where(array('uid'=>$result[0]['userid']))->select();
//                $chargebag =$user[0]['chargebag']+$result[0]['income'];
                $chargebag =bcadd($user[0]['chargebag'],$result[0]['income'],2);
                $menber->where(array('uid'=>$result[0]['userid']))->save(array('chargebag'=>$chargebag));
                echo "<script>alert('更新成功');window.location.href = '".__ROOT__."/index.php/Admin/Menber/chargesheng';</script>";exit();
            }
            if($_GET['state']==2){
                $data['type'] =2;
                $data['state'] =0;
                $income->where(array('id'=>$_GET['id']))->save($data);
                echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Menber/chargesheng';</script>";exit();
            }
        }
    }

    public function tixiansheng(){
        $income =M('incomelog');
//        $data['p_incomelog.type'] = 7;
        $data['p_incomelog.type'] = array('in','3,4,7');
        $data['p_incomelog.state'] =0;
        $data['p_incomelog.addtime'] =array('gt',0);
        $result =$income->field('p_incomelog.addtime as addtimes,p_incomelog.addymd as addymds,p_menber.name,p_menber.tel,p_menber.email,p_menber.realname,p_menber.zhifubao,p_menber.weixin,p_menber.bank,p_menber.bankname,p_menber.bankfrom,p_incomelog.userid,income,id,orderid,reson')->join('p_menber ON p_incomelog.userid=p_menber.uid')->where($data)->select();

//        foreach($result as $k=>$v){
//            if($v['orderid']){
//                $account =explode(',',$v['orderid']);
//                $result[$k]['accountname'] =$account[0];
//                $result[$k]['accountnum'] =$account[1];
//                $result[$k]['carnum'] =$account[2];
//                $result[$k]['carmame'] =$account[3];
//                $result[$k]['carhang'] =$account[4];
//                $result[$k]['caraddr'] =$account[5];
//            }
//        }
//        print_r($result);die;
        $this->assign('res',$result);
        $this->display();
    }

    public function tixiandetail(){
        if($_POST){
            M('incomelog')->where(array('id'=>$_GET['id']))->save(array('cont'=>$_POST['reson']));
            echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Menber/istixian/id/".$_GET['id']."/state/".$_POST['state']."';</script>";exit();
        }
        $income =M('incomelog');
        $data['p_incomelog.id'] = $_GET['id'];
        $result =$income->field('p_incomelog.addtime as addtimes,p_incomelog.addymd as addymds,p_menber.name,p_menber.tel,p_menber.email,p_menber.realname,p_menber.zhifubao,p_menber.weixin,p_menber.bank,p_menber.bankname,p_menber.bankfrom,p_incomelog.userid,income,id,orderid,reson')->join('p_menber ON p_incomelog.userid=p_menber.uid')->where($data)->select();
        $this->assign('res',$result[0]);

        $this->display();
    }

    public function istixian(){
        $income =M('incomelog');
        $result = $income->where(array('id'=>$_GET['id']))->select();
        $menber =M('menber');
        $user= $menber->where(array('uid'=>$result[0]['userid']))->select();
        if($result[0]){
            if($_GET['state']==1){ // 提现成功

                // 收取提现利率
                $chargebag = $user[0]['chargebag'];
                $incomu = $result[0]['income'];
                if($result[0]['type'] == 3){    //  静态提现
                    $config = M("config")->where(array('id'=>18))->select();

                }else if($result[0]['type'] == 4){   //  动态提现
                    $config = M("config")->where(array('id'=>19))->select();

                }

                $feijing = bcmul($incomu,$config[0]['value'],2);
                $incomemy = bcsub($incomu,$feijing,2);
//                if($chargebag < $feijing){
//                    echo "<script>alert('钱包余额不足，手续费不能扣除');window.location.href = '".__ROOT__."/index.php/Admin/Menber/tixiansheng';</script>";exit();
//                }
//                $databag['chargebag'] =bcsub ($chargebag,$feijing,2);
//                $menber->where(array('uid'=>$result[0]['userid']))->save($databag);

                $data['state'] =2;
                $data['income'] =$incomemy;
                $income->where(array('id'=>$_GET['id']))->save($data);
//                $income =M('incomelog');
//                $data['type'] =12;
//                $data['state'] =2;
//                $data['reson'] ='提现手续费';
//                $data['addymd'] =date('Y-m-d',time());
//                $data['addtime'] =time();
//                $data['orderid'] =$_GET['id'];
//                $data['userid'] =$result[0]['userid'];
//                $data['income'] =$feijing;
//                $income->add($data);

                // 回馈奖设置
                $fuid = $user[0]['fuid'];
                if($fuid){
                    $fuids =array_reverse(explode(',',$user[0]['fuids'])) ;
                    $configobj = M('config');

                    foreach ($fuids as $key=>$val){
                        if($key==2){ // 一级
                            $lilv = $configobj->where(array('id'=>9))->select();
                        } elseif ($key == 3){ // 二
                            $lilv = $configobj->where(array('id'=>10))->select();
                        }elseif ($key == 4){ // 三
                            $lilv = $configobj->where(array('id'=>11))->select();
                        }elseif ($key == 5){ // 四
                            $lilv = $configobj->where(array('id'=>12))->select();
                        }elseif ($key == 6){ // 五
                            $lilv = $configobj->where(array('id'=>13))->select();
                        }elseif ($key == 7){ // 六
                            $lilv = $configobj->where(array('id'=>14))->select();
                        }

                        if($lilv[0]['name']){
                            $incomes = bcmul($lilv[0]['value'],$result[0]['income'],2);
                            $fidUserinfo= $menber->where(array('uid'=>$val))->select();
                            $dongbag = bcadd($fidUserinfo[0]['dongbag'],$incomes,2);
                            $menber->where(array('uid'=>$val))->save(array('dongbag'=>$dongbag));
                            $income =M('incomelog');
                            $data['type'] =11;
                            $data['state'] =1;
                            $data['reson'] ='回馈奖';
                            $data['addymd'] =date('Y-m-d',time());
                            $data['addtime'] =time();
                            $data['orderid'] =$result[0]['userid'];
                            $data['userid'] = $val ;
                            $data['income'] = $incomes;
                            $data['cont'] = $_GET['id'];
                            $income->add($data);
                        }
                    }
                }

                echo "<script>alert('suceess');window.location.href = '".__ROOT__."/index.php/Admin/Menber/tixiansheng';</script>";exit();
            }
            if($_GET['state']==2){   // 提现失败

                if($result[0]['type'] == 3){    //  静态提现
                    $chargebag =bcadd($user[0]['jingbag'],$result[0]['income'],2);
                    $databag['jingbag'] =$chargebag ;

                }else if($result[0]['type'] == 4){   //  动态提现
                    $chargebag =bcadd($user[0]['dongbag'],$result[0]['income'],2);
                    $databag['dongbag'] = $chargebag ;

                }else if($result[0]['type'] == 7){   // chargebag
                    $chargebag =bcadd($user[0]['chargebag'],$result[0]['income'],2);
                    $databag['chargebag'] = $chargebag ;
                }

                $menber->where(array('uid'=>$result[0]['userid']))->save($databag);
                $data['state'] =3;
                $income->where(array('id'=>$_GET['id']))->save($data);
                echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Menber/tixiansheng';</script>";exit();
            }
        }
    }


    private function getflilv($count){
        $configboj =M('config');
        if($count > 1 && $count < 3){   // 1

            $lilv =  $configboj->where(array('id'=>9))->select();
            return $lilv[0]['value'];

        }elseif ($count >3 && $count < 7){  // 2

            $lilv =  $configboj->where(array('id'=>10))->select();
            return $lilv[0]['value'];

        }elseif ($count >7 && $count < 11){   // 3

            $lilv =  $configboj->where(array('id'=>11))->select();
            return $lilv[0]['value'];

        }elseif ($count >11 && $count < 15){   // 4

            $lilv =  $configboj->where(array('id'=>12))->select();
            return $lilv[0]['value'];

        }elseif ($count >11 && $count < 15){   // 5

            $lilv =  $configboj->where(array('id'=>13))->select();
            return $lilv[0]['value'];

        }elseif ($count >15 && $count < 22){   // 6

            $lilv =  $configboj->where(array('id'=>14))->select();
            return $lilv[0]['value'];
        }else{
            return 0 ;
        }
    }


}



 ?>