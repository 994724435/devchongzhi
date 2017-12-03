<?php
namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class IndexController extends CommonController {

    public function telCharge(){
        if($_POST['tel']){
            $appkey ='90b0b7d6589571ae32f7cef0e91de4e6';
            $isurl ="http://op.juhe.cn/ofpay/mobile/telcheck?cardnum=".$_POST['number']."&phoneno=".$_POST['tel']."&key=$appkey";
            $iscanchong =$this->curlget($isurl);
            if($iscanchong['error_code'] != 0){
                $errmsg =$iscanchong['reason'];
                echo "<script>alert('".$errmsg."');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/telCharge.html';";
                echo "</script>";
                exit;
            }

            $member = M("menber");
            $userinfo =$member->where(array('uid'=>session('uid')))->find();
            if($userinfo['chargebag'] < $_POST['number']){
                $errmsg ="当前余额不足";
                echo "<script>alert('".$errmsg."');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/telCharge.html';";
                echo "</script>";
                exit;
            }

            if($userinfo['pwd2'] != $_POST['pwd2']){
                $errmsg ="充值密码错误";
                echo "<script>alert('".$errmsg."');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/telCharge.html';";
                echo "</script>";
                exit;
            }

            $tel =$_POST['tel'];
            $number =$_POST['number'];
            $times =time();
            $str ='JH4afc38ae575792b6bf3845455a1238e5'.$appkey.$_POST['tel'].$_POST['number'].$times;
            $sign =md5($str);
            $geturl='http://op.juhe.cn/ofpay/mobile/onlineorder?'."phoneno=$tel&cardnum=$number&orderid=$times&sign=$sign&key=$appkey";

            $res_curl =$this->curlget($geturl);
            if($res_curl['error_code'] ==0){
                $data['type'] =8;
                $data['state'] =2;
                $data['reson'] ='话费充值';
                $data['addymd'] =date('Y-m-d',time());
                $data['addtime'] =$times;
                $data['orderid'] =$times;
                $data['tel'] =$tel;
                $data['userid'] =session('uid');
                $data['income'] =$number;
                M('incomelog')->add($data);
                $chargemoney =bcsub($userinfo['chargebag'],$_POST['number'],2);
                $member->where(array('uid'=>session('uid')))->save(array('chargebag'=>$chargemoney));
                $errmsg ="充值成功";
                echo "<script>alert('".$errmsg."');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/telCharge.html';";
                echo "</script>";
                exit;
            }else{
                $errmsg =$res_curl['reason'];
                echo "<script>alert('".$errmsg."');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/telCharge.html';";
                echo "</script>";
                exit;
            }

        }
        $this->display();
    }



    public function  assets(){
        $pro = M('incomelog')->where(array('userid'=>session('uid'),'type'=>1))->sum('income');
        $pro = bcadd($pro,0,2);
        $this->assign('incomes',$pro);
        $this->display();
    }

    public function  assetsin(){
        if($this->isdong()){
            echo "<script>alert('智能余额已被冻结');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/assets.html';";
            echo "</script>";
            exit;
        }
        $member = M("menber");
        $userinfo =$member->where(array('uid'=>session('uid')))->find();
        $allmoney =bcadd($userinfo['chargebag'],$userinfo['jingbag'],2);
        $member->where(array('uid'=>session('uid')))->save(array('chargebag'=>'0.00','jingbag'=>$allmoney));
        echo "<script>";
        echo "window.location.href='".__ROOT__."/index.php/Home/Index/assets.html';";
        echo "</script>";
        exit;
    }

    public function  assetsout(){
        if($this->isdong()){
            echo "<script>alert('智能余额已被冻结');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/assets.html';";
            echo "</script>";
            exit;
        }
        $member = M("menber");
        $userinfo =$member->where(array('uid'=>session('uid')))->find();
        $allmoney =bcadd($userinfo['chargebag'],$userinfo['jingbag'],2);
        $member->where(array('uid'=>session('uid')))->save(array('chargebag'=>$allmoney,'jingbag'=>'0.00'));
        echo "<script>";
        echo "window.location.href='".__ROOT__."/index.php/Home/Index/assets.html';";
        echo "</script>";
        exit;
    }

    private function isdong(){
        $config =M("config")->where(array('id'=>3))->find();
        if($config['value'] ==1){
            return 1;
        }else{
            return 0;
        }
    }

    public function mall(){
        $pro = M('product')->where(array('type'=>2,'state'=>1))->select();
        $this->assign('pro',$pro);
        $this->display();
    }

    public function shop(){
        $member = M("menber");
        $userinfo =$member->where(array('uid'=>session('uid')))->find();
        if($userinfo['type'] < 3){
            echo "<script>alert('空冲商城需豪华会员访问');";
            echo "window.location.href='".__ROOT__."/index.php/Home/User/my.html';";
            echo "</script>";
            exit;
        }
        $pro = M('product')->where(array('type'=>1,'state'=>1))->select();
        $this->assign('pro',$pro);
        $this->display();
    }

    /*
  * 1收益 2充值 3静态提现  4动态体现  5 注册下级 6下单购买 7积分体现 8积分转账 9 回馈奖 10牧场收益 11 幼崽收益 12成年 13母牦牛
   */
    public function withdraw(){
        $lilv =  M("config")->where(array('id'=>18))->find();
        $lilv =$lilv['value'];
        if($_POST){
            if($_POST['num']<=0){
                echo "<script>alert('请输入正确金额在');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/width_draw';";
                echo "</script>";
                exit;
            }
            $menber =M('menber');
            $res_user = $menber->where(array('uid'=>session('uid')))->select();
            if($res_user[0]['pwd2'] != $_POST['pwd2']){
                echo "<script>alert('二级密码错误');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/width_draw';";
                echo "</script>";
                exit;
            }

//            if($_POST['num'] <100){
//                echo "<script>alert('提现额度小于100');";
//                echo "window.location.href='".__ROOT__."/index.php/Home/User/width_draw';";
//                echo "</script>";
//                exit;
//            }

            $max = M("config")->where(array('id'=>19))->find();
            if($_POST['num'] > $max['value']){
                echo "<script>alert('提现额度大于".$max['value']."');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/width_draw';";
                echo "</script>";
                exit;
            }

//            $income =M('incomelog');
//            $istoday =$income->where(array('type'=>7,'userid'=>session('uid'),'addymd'=>date('Y-m-d',time())))->find();
//            if($istoday['userid']){
//                echo "<script>alert('每日提现允许一次');";
//                echo "window.location.href='".__ROOT__."/index.php/Home/User/width_draw';";
//                echo "</script>";
//                exit;
//            }

            $left = bcsub($res_user[0]['chargebag'],$_POST['num'],2);

            $lilcv = $lilv;
            $fei = bcmul($_POST['num'],$lilcv,2);
            $left = bcsub($left,$fei,2);
            if($left > 0){
                $re = $menber->where(array('uid'=>session('uid')))->save(array('chargebag'=>$left));
                if($re){
                    $income =M('incomelog');
                    $data['type'] =7;
                    $data['state'] =0;
                    $data['reson'] ='余额提现';
                    $data['addymd'] =date('Y-m-d',time());
                    $data['addtime'] =time();
                    $data['orderid'] =session('uid');
                    $data['userid'] =session('uid');
                    $data['income'] =$_POST['num'];
                    $income->add($data);
                    $resreson ="提现".$_POST['num']."元";
                    echo "<script>alert('".$resreson."待管理员确认');";
                    echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
                    echo "</script>";
                    exit;
                }
            }else{
                echo "<script>alert('余额不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/User/my';";
                echo "</script>";
                exit;
            }

        }
        $this->assign('lilv',$lilv);
        $this->display();
    }


    //主页
	public function index(){
		$pro =M('product');
		$prolist= $pro->order('id DESC')->where(array('state'=>1))->select();
		// 牛信息
        $you = M("orderlog")->where(array('userid'=>session('uid'),'state'=>1,'type'=>2))->count();
        $hei = M("orderlog")->where(array('userid'=>session('uid'),'state'=>1,'type'=>3))->count();
        $mu = M("orderlog")->where(array('userid'=>session('uid'),'state'=>1,'type'=>4))->count();
        $income = M("incomelog")->where(array('userid'=>session('uid')))->order('id DESC')->select();

        $this->assign('you',$you);
        $this->assign('hei',$hei);
        $this->assign('mu',$mu);
		$this->assign('list',$prolist);
        $this->assign('income',$income);
        $this->assign('userlist',$this->getuser(session('uid')));
		$this->display();
	}

    private function getuser($uid){
        $user =array();
        $member = M("menber");
        for ($i=0;$i<=7;$i++){
            if($i == 0){
                $user[0] =$member->field('uid,tel,name,fuids,addtime,addymd')->where(array('fuid'=>$uid))->select();
            }else{
                if($user[$i-1]){
                    $array =array();
                    foreach ($user[$i-1] as $k=>$v){
                        $temp= $member->field('uid,tel,name,fuids,addtime,addymd')->where(array('fuid'=>$v['uid']))->select();
                        foreach ($temp as $v1){
                            array_push($array,$v1);
                        }
                    }
                    $user[$i] =$array;
                }
            }
        }
        return $user;
    }

    //买商品
    public function buymall(){
        if($_GET['id']){
            $users = M("menber")->where(array('uid'=>session('uid')))->find();

            $pro = M("product")->where(array('id'=>$_GET['id']))->find();
            $allmoney =bcmul($pro['price'],1,2);
            if($users['dongbag'] < $allmoney){
                echo "<script>alert('当前余额不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/mall.html';";
                echo "</script>";
                exit;
            }

            $order['userid'] =session('uid');
            $order['productid'] =$pro['id'] ;
            $order['productname'] =$pro['name'];
            $order['productmoney'] =$users['price'];
            $order['state'] = 1;
            $order['type'] = $pro['type'] ;
            $order['orderid'] = time();
            $order['addtime'] = time();
            $order['addymd'] = date("Y-m-d",time());
            $order['num'] = 1;
            $order['price'] =$pro['price'];
            $order['totals'] =$allmoney;
            $order['option'] =$pro['type'] ;
            if($allmoney > 0){
                $logid =  M("orderlog")->add($order);
            }

            $income =M('incomelog');
            $data['type'] =10;
            $data['state'] =2;
            $data['reson'] ='下单购买';
            $data['addymd'] =date('Y-m-d',time());
            $data['addtime'] =time();
            $data['orderid'] =$logid;
            $data['userid'] =session('uid');
            $data['income'] =$allmoney;
            if($pro['price'] > 0){
                $income->add($data);
            }

            $menber = M("menber");
            $userinfo = $menber->where(array('uid'=>session('uid')))->find();
            $chargebag = bcsub($userinfo['dongbag'],$allmoney,2);
            $menber->where(array('uid'=>session('uid')))->save(array('dongbag'=>$chargebag));

            echo "<script>alert('购买成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
            echo "</script>";
            exit;
        }else{
            echo "<script>";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
            echo "</script>";
            exit;
        }
    }

    //买商品
    public function buyproduct(){
        if($_GET['id']){
            $users = M("menber")->where(array('uid'=>session('uid')))->find();

            $pro = M("product")->where(array('id'=>$_GET['id']))->find();
            $allmoney =bcmul($pro['price'],1,2);
            if($users['chargebag'] < $allmoney){
                echo "<script>alert('当前余额不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/shop.html';";
                echo "</script>";
                exit;
            }

            $order['userid'] =session('uid');
            $order['productid'] =$pro['id'] ;
            $order['productname'] =$pro['name'];
            $order['productmoney'] =$users['name'];
            $order['state'] = 1;
            $order['type'] = $pro['type'] ;
            $order['orderid'] = time();
            $order['addtime'] = time();
            $order['addymd'] = date("Y-m-d",time());
            $order['num'] = 1;
            $order['price'] =$pro['price'];
            $order['totals'] =$allmoney;
            $order['option'] =$pro['type'] ;
            if($allmoney > 0){
              $logid =  M("orderlog")->add($order);
            }

            $income =M('incomelog');
            $data['type'] =6;
            $data['state'] =2;
            $data['reson'] ='下单购买';
            $data['addymd'] =date('Y-m-d',time());
            $data['addtime'] =time();
            $data['orderid'] =$logid;
            $data['userid'] =session('uid');
            $data['income'] =$allmoney;
            if($pro['price'] > 0){
                $income->add($data);
            }

            $menber = M("menber");
            $userinfo = $menber->where(array('uid'=>session('uid')))->find();
            $chargebag = bcsub($userinfo['chargebag'],$allmoney,2);
            $menber->where(array('uid'=>session('uid')))->save(array('chargebag'=>$chargebag));

            echo "<script>alert('购买成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
            echo "</script>";
            exit;
        }else{
            echo "<script>";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
            echo "</script>";
            exit;
        }

    }


	//我的产品
	public function financial(){
		$orderlog =M('orderlog');
		$result  = $orderlog->join('p_product ON p_orderlog.productid=p_product.id')->where(array('userid'=>session('uid')))->select();
		foreach($result as $k=>$v){
			if($v['states']==0){
				$v['total'] = $v['prices'] *$v['num'];
				$data['wait'][] =$v;
			}
			if($v['states']==1){
				$v['total'] = $v['prices'] *$v['num'];
				$data['coming'][] =$v;
			}
			if($v['states']==2){
				$v['total'] = $v['prices'] *$v['num'];
				$data['comoever'][] =$v;
			}
		}
		$this->assign('res',$data);
		$this->display();
	}





    public function qrcode(){
        Vendor('phpqrcode.phpqrcode');
        $id=I('get.id');
        //生成二维码图片
        $object = new \QRcode();
        $url="http://".$_SERVER['HTTP_HOST'].'/index.php/Admin/Article/editearticle/id/'.$id;//网址或者是文本内容

        $level=3;
        $size=5;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }



    public function gongpai_buy(){
        if($_POST['num']){
            $menber = M('menber');
            $userinfo = $menber->where(array('uid'=>session('uid')))->select();
            if($_POST['num'] > $userinfo[0]['chargebag']){
                echo "<script>alert('充值钱包余额不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/gongpai_buy';";
                echo "</script>";
                exit;
            }
            $left =bcsub( $userinfo[0]['chargebag'],$_POST['num'],2);
            $menber->where(array('uid'=>session('uid')))->save(array('chargebag'=>$left));

            $orderlog = M('orderlog');
            $allorder = $orderlog->where(array('type'=>2))->order('logid DESC')->select();
            $allcount =count($allorder);
            if($allorder[0]){
                $bianhao = $allorder[0]['bianhao'] + 1;
                $num = $allorder[0]['num'] + 1;
                $ceng = $this->getceng($allcount) ;

                // 处理层级关系
                $isaddcen = $this->isaddceng($allcount);
                if($isaddcen){
                    foreach ($allorder as $k=>$v){
                        $afterceng =$v['ceng']+1;
                        $orderlog->where(array('logid'=>$v['logid']))->save(array('ceng'=>$afterceng));
                        $fengs = bcpow(2,$afterceng) ;
                        $fengs = bcmul (4,$fengs) ;
                        if($v['userid']){   // 积分增加
                            $newuser = $menber->where(array('uid'=>$v['userid']))->select();
                            $newfeng = $fengs;
                            $dongbag = $newuser[0]['dongbag'] + $fengs;
                            $menber->where(array('uid'=>$v['userid']))->save(array('jifeng'=>$newfeng,'dongbag'=>$dongbag));

                            // 收入日志
                            $income =M('incomelog');
                            $data['type'] = 11 ;
                            $data['state'] = 1 ;
                            $data['reson'] ='公排收益';
                            $data['addymd'] =date('Y-m-d',time());
                            $data['addtime'] =time();
                            $data['orderid'] =session('uid');
                            $data['userid'] = $v['userid'];
                            $data['income'] = $fengs;
                            $income->add($data);

                        }
                    }
                }
            }else{
                $ceng = 1;
                $bianhao = 10000;
                $num =1;
            }

            // 下单
            $orderdata['userid'] =session('uid');
            $orderdata['productname'] ='购买公排';
            $orderdata['productmoney'] =$_POST['num'];
            $orderdata['states'] = 1 ;
            $orderdata['orderid'] =$bianhao;
            $orderdata['addtime'] =time();
            $orderdata['num'] = $num ;
            $orderdata['prices'] =$_POST['num'];
            $orderdata['addymd'] =date('Y-m-d',time());
            $orderdata['type'] =  2;
            $orderdata['ceng'] = 0;
            $orderdata['bianhao'] = $bianhao;
            $orderdata['totals'] =$_POST['num'];
            $logid = $orderlog->add($orderdata);

            // 收入日志
            $income =M('incomelog');
            $data['type'] =6;
            $data['state'] =2;
            $data['reson'] ='购买公排';
            $data['addymd'] =date('Y-m-d',time());
            $data['addtime'] =time();
            $data['orderid'] =$logid;
            $data['userid'] = session('uid');
            $data['income'] = $_POST['num'];
            $income->add($data);

            echo "<script>alert('购买成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/gongpai';";
            echo "</script>";
            exit;
        }

        $config = M('config')->where(array('id'=>17))->select();
        $this->assign('config',$config[0]['value']);
        $this->display();
    }

    private function isaddceng($cen){
        if(in_array($cen,array(1,3,7,15,31,63,127,255,511))){
            return 1;
        }else{
            return 0;
        }
    }



    private function getceng($count){
        if($count ==0 ){     // 1
            return 1;
        }elseif ($count >=1 && $count <3){   // 2
            return 2;
        }elseif ($count >=3 && $count <7){   // 3
            return 3;
        }elseif ($count >=7 && $count <15){  // 4
            return 4;
        }elseif ($count >=15 && $count <31){  // 5
            return 5;
        }elseif ($count >=31 && $count <63){   // 6
            return 6;
        }elseif ($count >=63 && $count <127 ){  // 7
            return 7;
        }elseif ($count >=127 && $count <255){  // 8
            return 8;
        }elseif ($count >=255 && $count <511){  // 9
            return 9;
        }elseif ($count >=511 && $count <1024){     // 10
            return 10;
        }
    }

    // 1首页 2公告 3值班团队 4分析专家 5公司简介  gruop

    public function types(){
        $type = isset($_GET['type']) ? $_GET['type']: 2 ;
        if($type ==2){
            $title = "公告列表";
        }elseif ($type == 3){
            $title = "值班团队";
        }elseif ($type == 4){
            $title = "分析专家";
        }
        $article =M('article');
        $intro= $article->order('aid DESC')->where(array('type'=>$type))->select();
        $this->assign('title',$title);
        $this->assign('res',$intro);
        $this->display();
    }

    /**
	 * 获取当前页面完整URL地址
	 */
	private function get_url() {
		$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
		$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
		$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
		return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
	}


	private function getlists($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}

	private function curlget($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
//		执行并获取HTML文档内容
		$output = curl_exec($ch);
		//释放curl句柄
		curl_close($ch);
		return json_decode($output, true);
	}
}