<?php
/**
 * Created by PhpStorm.
 * User: JONE
 * DateTime: 2018/9/29 22:27
 * Email: abc@jone.xyz
 * Description: 错误信息处理类
 */

class Err
{
    /**
     * 正常模式错误处理
     * @param $info
     *
     */
    public function regular($info)
    {
       exit('数据库错误,请稍后再试');
    }

    /**
     * debug模式错误处理
     * @param $info
     */
    public function debug($info)
    {
        echo $info;
        echo '<hr>';
    }

}