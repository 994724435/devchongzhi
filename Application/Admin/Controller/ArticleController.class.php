<?php
namespace Admin\Controller;
use Think\Controller;
class ArticleController extends CommonController {
	public function lists(){
        $article =M('article');
        if($_GET['type']){
            $res = $article->where(array('type'=>$_GET['type']))->select();
        }else{
            $res = $article->select();
        }

        $this->assign('res',$res);
        $this->display();
    }

    public function addarticle(){
        if($_POST['title']){
            $article =M('article');
            $data['type'] =$_POST['type'];
            $data['title'] =$_POST['title'];
            $data['cont'] =$_POST['content1'];
            $data['addtime'] =date('Y-m-d H:i:s');
            $data['addymd'] =date('Y-m-d');
            $data['admin'] =$_SESSION['uname'];
            $result = $article->add($data);
            if($result){
                echo "<script>alert('添加成功');window.location.href = '".__ROOT__."/index.php/Admin/Article/lists';</script>";
            }
        }
        $this->display();
    }

    public function delete(){
        $article =M('article');
        if($_GET['id']==1){
            echo "<script>alert('公司简介不能删除');window.location.href = '".__ROOT__."/index.php/Admin/Article/lists';</script>";exit();
        }
        $res =$article->where(array('aid'=>$_GET['id']))->delete();
        if($res){
            echo "<script>alert('删除成功');window.location.href = '".__ROOT__."/index.php/Admin/Article/lists';</script>";exit();
        }
    }

    public function editearticle(){
        $article =M('article');
        if($_POST['title']){
//            $data['type'] =$_POST['type'];
            $data['title'] =$_POST['title'];
            $data['cont'] =$_POST['content1'];
            $data['addtime'] =date('Y-m-d H:i:s');
            $data['addymd'] =date('Y-m-d');
            $data['admin'] =$_SESSION['uname'];
            $result = $article->where(array('aid'=>$_GET['id']))->save($data);
            if($result){
                echo "<script>alert('修改成功');window.location.href = '".__ROOT__."/index.php/Admin/Article/lists';</script>";exit();
            }
        }
        $res = $article->where(array('aid'=>$_GET['id']))->select();
        $this->assign('res',$res[0]);
        $this->display();
    }
}



 ?>