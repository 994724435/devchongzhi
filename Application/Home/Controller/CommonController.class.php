<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize(){
		$function = explode('/',__ACTION__);
		$curfunction =$function[count($function)-1];
		session('uid',1);
		if(!session('uid')){
			echo "<script>";
			echo "window.location.href='".__ROOT__."/index.php/Home/Login/login';";
			echo "</script>";
			exit;
		}
		$menber =M('menber');
		$res_user =$menber->where(array('uid'=>session('uid')))->select();
		if($res_user[0]['isdelete']){
            echo "<script>alert('账号已被禁用');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Login/login';";
            echo "</script>";
            exit;
        }
//		$this->assign('function',$this->getfunction($curfunction));
        $one = bcadd($res_user[0]['djbag'],$res_user[0]['jingbag'],2);
		$alls = bcadd($one,$res_user[0]['chargebag'],2);
        $this->assign('alls',$alls);
		$this->assign('username',$res_user[0]);
//		$this->assign('usertype',$this->chanefortype($res_user[0]['type']));
	}

	private function getfunction($curfunction){
		if($curfunction=='index'){
			return 1;
		}elseif($curfunction=='financial'){
			return 2;
		}elseif($curfunction=='product'){
			return 3;
		}elseif($curfunction=='user'){
			return 4;
		}else{
			return 1;
		}
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

}