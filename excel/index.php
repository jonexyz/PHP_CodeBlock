<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-09-29
 * Time: 13:07
 */

// 异步请求url
function _sock($url) {
    $host = parse_url($url,PHP_URL_HOST);
    $port = parse_url($url,PHP_URL_PORT);
    $port = $port ? $port : 80;
    $scheme = parse_url($url,PHP_URL_SCHEME);
    $path = parse_url($url,PHP_URL_PATH);
    $query = parse_url($url,PHP_URL_QUERY);
    if($query) $path .= '?'.$query;
    if($scheme == 'https') {
        $host = 'ssl://'.$host;
    }

    $fp = fsockopen($host,$port,$error_code,$error_msg,1);
    if(!$fp) {
        return array('error_code' => $error_code,'error_msg' => $error_msg);
    }
    else {
        stream_set_blocking($fp,true);//开启了手册上说的非阻塞模式
        stream_set_timeout($fp,1);//设置超时
        $header = "GET $path HTTP/1.1\r\n";
        $header.="Host: $host\r\n";
        $header.="Connection: close\r\n\r\n";//长连接关闭
        fwrite($fp, $header);
        usleep(1000); // 这一句也是关键，如果没有这延时，可能在nginx服务器上就无法执行成功
        fclose($fp);
        return array('error_code' => 0);
    }
}

// 获取当前主机域名
function get_domain(){
    $scheme = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
    $url = $scheme.$_SERVER['HTTP_HOST'];

    return $url;
}

$url = get_domain().'/excel/excel.php';

/**
 * 为避免因执行excel.php文件造成当前页面执行时间过长(php挂起的问题)
 * 所以把当前页面通过异步http请求去执行
 */
_sock($url);



echo 'game over';