<?php

namespace Admin\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class QuanController extends CommonController {

    public function addproduct(){
        if($_POST['price']){
            $data['cont'] =$this->generate_code(6);
            $data['price'] =$_POST['price'];
            $data['addymd'] =date('Y-m-d H:i:s',time());
            $data['addtime'] =time();
            $data['state'] =1;

            $product =M('quan');
            $result = $product->add($data);
            if($result){
                echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Quan/productlist';</script>";
            }else{
                echo "<script>alert('添加失败');window.location.href = '".__ROOT__."/index.php/Admin/Quan/addproduct';</script>";
            }

        }
        $this->display();
    }

    public function productlist(){
        $product =M('quan');
        $result = $product->order('id desc')->select();
        $this->assign('res',$result);
        $this->display();
    }

    public function generate_code($length = 4) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $password;
    }

    public function editeproduct(){
        $product =M('product');
        if($_POST){
            $pic='';
            if($_FILES['thumb']['name']){   // 上传文件
                $thumb = imgFile();
                $info = $thumb['info'];
                if(!$info) {// 上传错误提示错误信息

                }else{// 上传成功
                    $path = $info['thumb']['savepath'];
                    $p = ltrim($path,'.');
                    $img = $info['thumb']['savename'];
                    $pic=$p.$img;
                    $pic=__ROOT__.$pic;
                }
            }
            $data['name'] =$_POST['name'];
            $data['type'] =$_POST['type'];
            $data['cont'] =$_POST['cont'];
            if($pic){
                $data['pic'] =$pic;
            }
            $data['price'] =$_POST['price'];
            $data['effectdays'] =$_POST['effectdays'];
            $data['daycome'] =$_POST['daycome'];
            $data['daynum'] =$_POST['daynum'];
            $data['one'] =$_POST['one'];
            $data['two'] =$_POST['two'];
            $data['addtime'] =date('Y-m-d H:i:s',time());
            $result = $product->where(array('id'=>$_GET['id']))->save($data);
            if($result){
                echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Index/productlist';</script>";
            }else{
                echo "<script>alert('修改失败');window.location.href = '".__ROOT__."/index.php/Admin/Index/productlist';</script>";
            }

        }
        $result = $product->where(array('id'=>$_GET['id']))->select();
        $this->assign('res',$result[0]);
        $this->display();
    }

    public function deleteproduct(){
        $product =M('quan');
        $result = $product->where(array('id'=>$_GET['id']))->select();
        if($result[0]){
            $state =$result[0]['state'];
        }else{
            echo "<script>alert('产品不存在');window.location.href = '".__ROOT__."/index.php/Admin/Index/productlist';</script>";
        }
        if($state==1){
            $state=2;
        }else{
            $state=1;
        }
        $res= $product->where(array('id'=>$_GET['id']))->save(array('state'=>$state));
        if($res){
            echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Quan/productlist';</script>";
        }else{
            echo "<script>alert('修改失败');window.location.href = '".__ROOT__."/index.php/Admin/Quan/productlist';</script>";
        }
    }

    public function select(){
        $orderlog = M('orderlog');
        if($_GET['state']){
//            $map['name']=array('like','%'.$_GET['name'].'%');
            $map['state'] =$_GET['state'];
        }
        if($_GET['uid']){
            $map['userid'] =$_GET['uid'];
        }
        if($_GET['orderid']){
            $map['orderid'] =$_GET['orderid'];
        }
        $map['type'] =10;
        $users= $orderlog->where($map)->order('logid DESC')->select();

        $this->assign('users',$users);
        $this->display();
    }

}