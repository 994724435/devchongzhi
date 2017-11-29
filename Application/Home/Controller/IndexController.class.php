<?php
namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class IndexController extends CommonController {
//	public function _initialize(){
//		if($_GET['openid']){
//			$menber =M('menber');
//			$user=$menber->where(array('openid'=>$_GET['openid']))->select();
//			S('name',$user[0]['name']);
//			S('userid',$user[0]['id']);
//			S('nickname',$user[0]['nickname']);
//		}
//	}
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
	private function getniuinfo($id){
	    if($id ==1){
	        return array('name'=>'地','price'=>100,'type'=>1,'num'=>1);
        }elseif ($id ==2){
            return array('name'=>'幼崽牦牛','price'=>1000,'type'=>2,'num'=>5);
        }elseif ($id ==3){
            return array('name'=>'黑牦牛','price'=>5000,'type'=>3,'num'=>3);
        }elseif ($id ==4){
            return array('name'=>'母牦牛','price'=>10000,'type'=>4,'num'=>2);
        }else{
            return 0;
        }
    }

    //买地买牛
    public function buyniu(){
        $users = M("menber")->where(array('uid'=>session('uid')))->find();
        $goodinfo =$this->getniuinfo($_GET['id']);
        if($goodinfo ==0){
            echo "<script>alert('系统异常');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
            echo "</script>";
            exit;
        }

        $count = M("orderlog")->where(array('userid'=>session('uid'),'type'=>$goodinfo['type']))->count();
        if($count >= $goodinfo['num']){
            $msg = $goodinfo['name']." 最多购买".$goodinfo['num']."个";
            echo "<script>alert('".$msg."');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
            echo "</script>";
            exit;
        }

        if($goodinfo['type'] > 1){
          $dicount =  M("orderlog")->where(array('userid'=>session('uid'),'type'=>1))->select();
          if(!$count[0]){
              $msg = "买牛必须先购买牧场";
              echo "<script>alert('".$msg."');";
              echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
              echo "</script>";
              exit;
          }
        }

        $pro = $goodinfo;
        $allmoney =$pro['price'];
        if($users['chargebag'] < $allmoney){
            echo "<script>alert('账户余额不足');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
            echo "</script>";
            exit;
        }

        $order['userid'] =session('uid');
        $order['productid'] =$_GET['id'];
        $order['productname'] =$pro['name'];
        $order['productmoney'] =$users['name'];
        $order['state'] = 1;
        $order['type'] = $pro['type'];
        $order['orderid'] = time();
        $order['addtime'] = time();
        $order['addymd'] = date("Y-m-d",time());
        $order['num'] = 1;
        $order['price'] =$pro['price'];
        $order['totals'] =$allmoney;
        $order['option'] ='';
        if($allmoney > 0){
            $logid =  M("orderlog")->add($order);
        }

        $income =M('incomelog');
        $data['type'] =6;
        $data['state'] =2;
        $data['reson'] ='购买'.$pro['name'];
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

        // 处理推荐奖
        $addmoney = bcmul($pro['price'],0.06,2);
        $addmoney =bcmul($addmoney,1.5,2);
        $data['type'] =4;
        $data['state'] =1;
        $data['reson'] ='牛气奖';
        $data['addymd'] =date('Y-m-d',time());
        $data['addtime'] =time();
        $data['orderid'] =$logid;
        $data['income'] =$addmoney;

        if($userinfo['fuid']){
            $data['userid'] =$userinfo['fuid'];
            $income->add($data);
            $this->addniu($userinfo['fuid'],$addmoney);
        }
        if($userinfo['two']){
            $data['userid'] =$userinfo['two'];
            $income->add($data);
            $this->addniu($userinfo['two'],$addmoney);
        }
        if($userinfo['three']){
            $data['userid'] =$userinfo['three'];
            $income->add($data);
            $this->addniu($userinfo['three'],$addmoney);
        }

        echo "<script>alert('购买成功');";
        echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
        echo "</script>";
        exit;
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


    //买商品
    public function buyproduct(){
        $article =M('product');
        if($_POST['num']){
            $users = M("menber")->where(array('uid'=>session('uid')))->find();

            $pro = M("product")->where(array('id'=>$_POST['goodsId']))->find();
            $allmoney =bcmul($pro['price'],$_POST['num'],2);
            if($users['dongbag'] < $allmoney){
                echo "<script>alert('商城积分不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
                echo "</script>";
                exit;
            }

            $order['userid'] =session('uid');
            $order['productid'] =$pro['id'] ;
            $order['productname'] =$pro['name'];
            $order['productmoney'] =$users['name'];
            $order['state'] = 1;
            $order['type'] = 10;
            $order['orderid'] = time();
            $order['addtime'] = time();
            $order['addymd'] = date("Y-m-d",time());
            $order['num'] = $_POST['num'];
            $order['price'] =$pro['price'];
            $order['totals'] =$allmoney;
            $order['option'] =$_POST['addr'].','.$_POST['username'].','.$_POST['tel'].','.$_POST['youbian'];
            if($_POST['num'] > 0){
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


    /**
     * 公司简介
     */
    public function introduce(){
        $article =M('article');
        $intro= $article->order('aid DESC')->where(array('type'=>5))->select();
        $this->assign('intro',$intro[0]);
        $this->display();
    }

    /**
     * 公告
     */
    public function advertising(){
        $article =M('article');
        $intro= $article->where(array('aid'=>$_GET['id']))->select();
        $this->assign('intro',$intro[0]);
        $this->display();
    }

    /**
     * 值班团队
     */
    public function gruop(){
        $article =M('article');
        $intro= $article->where(array('aid'=>$_GET['id']))->select();
        $this->assign('intro',$intro[0]);
        $this->display();
    }

    /**
     * 分析专家
     */
    public function professor(){
        $article =M('article');
        $intro= $article->where(array('aid'=>$_GET['id']))->select();
        $this->assign('intro',$intro[0]);
        $this->display();
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



    public function K(){
        $rite =M("rite")->order("id desc")->limit(7)->select();
        $this->assign('seven',$rite);
        $this->display();
    }

    public function choose(){
        $log = M('incomelog')->order('id DESC')->where(array('userid'=>session('uid'),'type'=>2))->select();
        $this->assign('log',$log);
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

    public function gongPai(){
        echo "<script>alert('显示公排暂未开放，敬请期待');";
        echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
        echo "</script>";
        exit;

        $orderlog = M('orderlog');
        $allorder = $orderlog->where(array('type'=>2,'userid'=>session('uid')))->order('logid ASC')->select();
//        print_r($allorder);die;
        $this->assign('res',$allorder[0]);
        $this->display();
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