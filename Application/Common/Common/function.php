<?php

/**
 * 使用curl获取远程数据
 * @param  string $url url连接
 * @return string      获取到的数据
 */
function curl_get_contents($url){
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);                //设置访问的url地址
    // curl_setopt($ch,CURLOPT_HEADER,1);               //是否显示头部信息
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);               //设置超时
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);   //用户访问代理 User-Agent
    curl_setopt($ch, CURLOPT_REFERER,$_SERVER['HTTP_HOST']);        //设置 referer
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);          //跟踪301
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
    $r=curl_exec($ch);
    curl_close($ch);
    return $r;
}

function getAdminPage($count,$nowPage,$num){
    $Page       = new \Think\AdminPage($count,$nowPage,$num);// 实例化分页类 传入总记录数和每页显示的记录数(25)

    $Page->setConfig('prev','上一页');
    $Page->setConfig('next','下一页');
    $Page->setConfig('first','首页');
    $Page->setConfig('last','末页');
    // $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    $Page->rollPage=5;
    $Page->lastSuffix=false;


    $show       = $Page->show();// 分页显示输出
    // return $show;
    return array(
            'show' => $show,
            'firstRow' => $Page->firstRow,
            'listRows' => $Page->listRows,
        );
}


function getPage($count,$num){
    $Page       = new \Think\Page($count,$num);// 实例化分页类 传入总记录数和每页显示的记录数(25)

    $Page->setConfig('prev','上一页');
    $Page->setConfig('next','下一页');
    $Page->setConfig('first','首页');
    $Page->setConfig('last','末页');
    // $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    $Page->rollPage=5;
    $Page->lastSuffix=false;


    $show       = $Page->show();// 分页显示输出
    // return $show;
    return array(
            'show' => $show,
            'firstRow' => $Page->firstRow,
            'listRows' => $Page->listRows,
        );
}

function imgFile(){
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize   =     3145728 ;// 设置附件上传大小
    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    $upload->rootPath  =     THINK_PATH; // 设置附件上传根目录
    $upload->savePath  =     '../Public/Uploads/'; // 设置附件上传（子）目录
    $info   =   $upload->upload();
    if(!$info){
        $error = $this->error($upload->getError());
    }
    return array(
        'info'=>$info,
        'error'=>$error,
        'rootPath'=>$upload->rootPath
        );
}


/**
 * 获取当前页面完整URL地址
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

function url(){
    $url = $_SERVER['PHP_SELF'];
    // print_r($url);die;
    $url .="?";
    if($_GET){
        foreach($_GET as $key=>$value){
            if($key != 'page'){
                $url .= "/{$key}/{$value}&"; 
            }
        }
    }
    return $url;
}

function list_page($current,$limit,$count,$url,$num=5,$class='aiston'){
    $get = strpos($url,'?');
    $pagecount = ceil($count/$limit);
    if($pagecount>1){
        if($get){
        $str = '';
        $str .= "<div class='{$class}'>";
        $str .= "<ul>";
        if($current!=1){
            $str.='<li><a href="'.$url.'&page=1">首页</a></li>';
            $str.='<li><a href="'.$url.'&page='.($current-1).'">上一页</a></li>';
        }
        // $num = 5;//分页显示的个数
        $tmp = floor($num/2);
        $page = ceil($count/$limit);//总页数
        if($num>$page){
            $s = 1;
            $e = $page;
        }else if($current>=$page-$tmp){
            $s = $page-$num+1;
            $e = $page;
        }else if($current<=$num-$tmp){
            $s = 1;
            $e = $num;
        }else{
            $s = $current-$tmp;
            $e = $current+$tmp;
        }

        for($i = $s;$i<=$e;$i++){
            if($e == 1){
                $str .= '';
            }else if($current == $i){
                $str .="<li class='color_orange'><a class='curr' href='{$url}&page={$i}'>{$i}</a></li>";
            }else{
                $str .="<li><a class='num1' href='{$url}&page={$i}'>{$i}</a></li>";
            }
            
        }
        if($current <$page){
            $tmp_page = $current+1;
            $str .= "<li><a href='{$url}&page={$tmp_page}'>下一页</a></li><li><a href='{$url}&page=$page'>尾页</a></li>";
        }else{
            $str .="<li class='scr'>下一页</li><li class='scr'>尾页</li>";
        }
        // $str.="<li id='news_last'>共{$page}页/{$count}条</li></ul></div>";
        $str.="</ul>";
        $str.="</div>";
        return $str;
    }else{
        $str = '';
        $str .= "<div class='{$class}'>";
        $str .= "<ul>";
        if($current!=1){
            $str.='<li><a href="'.$url.'?page=1">首页</a></li>';
            $str.='<li><a href="'.$url.'?page='.($current-1).'">上一页</a></li>';
        }
        // $num = 5;//分页显示的个数
        $tmp = floor($num/2);
        $page = ceil($count/$limit);//总页数
        if($num>$page){
            $s = 1;
            $e = $page;
        }else if($current>=$page-$tmp){
            $s = $page-$num+1;
            $e = $page;
        }else if($current<=$num-$tmp){
            $s = 1;
            $e = $num;
        }else{
            $s = $current-$tmp;
            $e = $current+$tmp;
        }

        for($i = $s;$i<=$e;$i++){ 
            if($current == $i){
                $str .="<li class='color_orange'><a class='curr' href='{$url}?page={$i}'>{$i}</a></li>";
            }else{
                $str .="<li><a class='num1' href='{$url}?page={$i}'>{$i}</a></li>";
            }
            
        }
        if($current <$page){
            $tmp_page = $current+1;
            $str .= "<li><a href='{$url}?page={$tmp_page}'>下一页</a></li><li><a href='{$url}?page=$page'>尾页</a></li>";
        }else if($page=1){
            // $str .="<li class='scr'>下一页</li><li class='scr'>尾页</li>";
        }else{
            $str .="<li class='scr'>下一页</li><li class='scr'>尾页</li>";
        }
        // $str.="<li id='news_last'>共{$page}页/{$count}条</li></ul></div>";
        $str.="</ul>";
        $str.="</div>";
        return $str;
    }
    }
}
function img_create_small($big_img, $width, $height, $small_img) {//原始大图地址，缩略图宽度，高度，缩略图地址
    $imgage = getimagesize($big_img); //得到原始大图片
    switch ($imgage[2]) { // 图像类型判断
        case 1:
        $im = imagecreatefromgif($big_img);
        break;
        case 2:
        $im = imagecreatefromjpeg($big_img);
        break;
        case 3:
        $im = imagecreatefrompng($big_img);
        break;
    }
    $src_W = $imgage[0]; //获取大图片宽度
    $src_H = $imgage[1]; //获取大图片高度
    $tn = imagecreatetruecolor($width, $height); //创建缩略图
    imagecopyresampled($tn, $im, 0, 0, 0, 0, $width, $height, $src_W, $src_H); //复制图像并改变大小
    imagejpeg($tn, $small_img); //输出图像
}
/**
 * 
 * 字符截取
 * @param string $string
 * @param int $start
 * @param int $length
 * @param string $charset
 * @param string $dot
 * 
 * @return string
 */
function str_cut(&$string, $start, $length, $charset = "utf-8", $dot = '...') {
    if(function_exists('mb_substr')) {
        if(mb_strlen($string, $charset) > $length) {
            return mb_substr ($string, $start, $length, $charset) . $dot;
        }
        return mb_substr ($string, $start, $length, $charset);
        
    }else if(function_exists('iconv_substr')) {
        if(iconv_strlen($string, $charset) > $length) {
            return iconv_substr($string, $start, $length, $charset) . $dot;
        }
        return iconv_substr($string, $start, $length, $charset);
    }

    $charset = strtolower($charset);
    switch ($charset) {
        case "utf-8" :
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $ar);
            if(func_num_args() >= 3) {
                if (count($ar[0]) > $length) {
                    return join("", array_slice($ar[0], $start, $length)) . $dot;
                }
                return join("", array_slice($ar[0], $start, $length));
            } else {
                return join("", array_slice($ar[0], $start));
            }
            break;
        default:
            $start = $start * 2;
            $length   = $length * 2;
            $strlen = strlen($string);
            for ( $i = 0; $i < $strlen; $i++ ) {
                if ( $i >= $start && $i < ( $start + $length ) ) {
                    if ( ord(substr($string, $i, 1)) > 129 ) $tmpstr .= substr($string, $i, 2);
                    else $tmpstr .= substr($string, $i, 1);
                }
                if ( ord(substr($string, $i, 1)) > 129 ) $i++;
            }
            if ( strlen($tmpstr) < $strlen ) $tmpstr .= $dot;
            
            return $tmpstr;
    }
}



 ?>