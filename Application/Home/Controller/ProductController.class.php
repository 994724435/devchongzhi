<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class ProductController extends CommonController{

    //理财产品
    public function product(){
        $product =M('product');
        $result  = $product->order('id asc')->select();
        $orderlog = M('orderlog');
        foreach($result as $k=>$v){
            $condtion ['state'] =array('in','1,2');
            $condtion['productid']=$v['id'];
            $res = $orderlog->where($condtion)->count();
            $result[$k]['salenum'] =$res;
        }
        $this->assign('res',$result);
        $this->display();
    }

    // 详情
    public function detail(){
        $product =M('product');
        $result  = $product->where(array('id'=>$_GET['id']))->select();
        if($_POST){
            $data['num'] =$_POST['number'];
            $data['prices'] =$result[0]['price'];
            $data['userid'] =session('uid');
            $data['productid'] =$_GET['id'];
            $data['productname'] =$result[0]['name'];
            $data['productmoney'] =$result[0]['daycome'];
            $data['states']       =0;
            $data['addtime']       =date('Y-m-d H:i:s',time());
            $data['orderid'] = date('YmdHis',time()).rand(1000,9999);
//            if(empty(session('uid')||empty($result[0]['name']))){
//                print_r("此产品不存在");die;
//            }
            $orderlog =M('orderlog');
            $res_order = $orderlog->add($data);
            if($res_order){
                echo "<script>";
                echo "window.location.href='".__ROOT__."/index.php/Home/Product/orderDetail/orderid/".$data['orderid']."';";
                echo "</script>";
                exit;
            }else{
                echo "<script>alert('下单失败');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Product/product';";
                echo "</script>";
                exit;
            }
        }
        $this->assign('res',$result[0]);
        $this->display();
    }

    // 支付页面
    public function orderDetail(){
        $orderlog =M('orderlog');
        $result  = $orderlog->where(array('orderid'=>$_GET['orderid']))->select();
        $total =$result[0]['prices']*$result[0]['num'];
        $result[0]['total'] =$total;
        $this->assign('res',$result[0]);
        $this->display();
    }

    public function deleteorder(){
        $orderlog =M('orderlog');
        $result  = $orderlog->where(array('orderid'=>$_GET['orderid']))->select();
        if(!$result[0]||$result[0]['states']!=0){
            echo "<script>alert('订单不存在');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/financial';";
            echo "</script>";
            exit;
        }
        $orderlog->where(array('orderid'=>$_GET['orderid']))->delete();
        echo "<script>alert('删除成功');";
        echo "window.location.href='".__ROOT__."/index.php/Home/Index/financial';";
        echo "</script>";
        exit;
    }

    //支付处理
    public function dealorder(){

        $orderlog =M('orderlog');
        $result  = $orderlog->where(array('orderid'=>$_GET['orderid']))->select();
        if(!$result[0]||$result[0]['states']!=0){
            echo "<script>alert('订单不存在');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/financial';";
            echo "</script>";
            exit;
        }
        $map['userid']=session('uid');
        $map['states']=array('in','1,2');
        $res_order =$orderlog->where($map)->select();
        if($res_order[0]||$result[0]['num']>=2){
            echo "<script>alert('每个用户只能选择一种套餐');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/financial';";
            echo "</script>";
            exit;
        }
        $total =$result[0]['prices']*$result[0]['num'];
        $menber =M('menber');
        $user_res =$menber->where(array('uid'=>session('uid')))->select();
        if($user_res[0]['chargebag']<$total){
            echo "<script>alert('充值钱包余额不足');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/financial';";
            echo "</script>";
            exit;
        }
        $res_money =bcsub($user_res[0]['chargebag'],$total,2);
        $menber->where(array('uid'=>session('uid')))->save(array('chargebag'=>$res_money));

        $orderlog->where(array('orderid'=>$_GET['orderid']))->save(array('states'=>1));
        $datas['state'] = 2;
        $datas['reson'] = "购买".$result[0]['productname'];
        $datas['type'] = 6;
        $datas['addymd'] = date('Y-m-d',time());
        $datas['addtime'] = date('Y-m-d H:i:s',time());
        $datas['orderid'] = $_GET['orderid'];
        $datas['userid'] = session('uid');
        $datas['income'] = $total;
        $this->savelog($datas);
        $menber->where(array('uid'=>session('uid')))->save(array('type'=>$this->changetype($result[0]['prices'])));
        echo "<script>alert('购买成功');";
        echo "window.location.href='".__ROOT__."/index.php/Home/Index/financial';";
        echo "</script>";
        exit;
    }

    private function changetype($num){
        if($num==800){
            return 1;
        }
        if($num==1500){
            return 2;
        }
        if($num==3000){
            return 3;
        }
        if($num==6000){
            return 4;
        }
    }

    private function savelog($data){
        $incomelog =M('incomelog');
        return $incomelog->add($data);
    }
}