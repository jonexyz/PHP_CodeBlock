<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;  // 表单验证
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{
    protected $config ='';

    public function __construct(){

        $appid = get_setting('wechat_appid');
        $pay_mchid = get_setting('pay_mchid');
        $pay_apikey = get_setting('pay_apikey');

        //微信支付参数配置(appid,商户号,支付秘钥)
        $config = array(
            'appid'		 => $appid,
            'pay_mchid'	 => $pay_mchid,
            'pay_apikey' => $pay_apikey,
        );
        $this->config = $config;
    }

    /**
     * 预支付请求接口(POST)
     * @param string $openid 	openid
     * @param string $body 		商品简单描述
     * @param string $order_sn  订单编号
     * @param string $total_fee 金额
     * @return  json的数据
     */
    public function index(){
        $config = $this->config;


        $input = request()->all();  //获取请求提交的所有的参数
        $rules = [    // 验证规则  required 必须参数
            'openid' => 'required',  //微信用户身份标识
            'body'=>'required',   // 商品名
            'money'=>'required', // 订单金额
            //'type' => 'required', //支付途径 根据此参数判断动态选择回调地址
        ];

        $messages = [  // 不符合验证规则的错误提示信息
            'openid.required' => 'openid不能为空',
            //'type.required' => 'type不能为空',
            'body.required' => 'body不能为空',
            'money.required' => 'money不能为空',

        ];

        $errors = Validator::make($input, $rules, $messages)->errors()->all();  //调用验证类 返回信息为错误提示信息

        if ($errors) return response()->json(err($errors[0])); //当错误时返回错误信息并终止程序执行


        $openid = $input['openid'];
        
        // 生成订单号
        $order_sn = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  


        // 组装订单生成所需数据
        $order_data['openid'] = $openid;  // 付款人openid
        $order_data['order_no'] = $order_sn;   // 订单号
        $order_data['money'] = $input['money'];   // 订单金额  分
        $order_data['mch_id'] = $config['pay_mchid']; // 收款商户号
        $order_data['create_time'] = date('Y-m-d H:i:s');
        $order_data['about'] = $input['body'];  // 产品说明
        $order_data['status'] = 0;  // 订单状态 未付款


        //dd($order_data);
        $order_id =DB::table('order')->insertGetId($order_data);

        if(!$order_id) return response()->json(succ('订单生成失败'));

        //统一下单参数构造
        $unifiedorder = array(
            'appid'			=> $config['appid'],
            'mch_id'		=> $config['pay_mchid'],
            'nonce_str'		=> self::getNonceStr(),
            'body'			=> $input['body'],
            'out_trade_no'	=> $order_sn,
            'total_fee'		=> $order_data['money'],
            'spbill_create_ip'	=> request()->ip(),
            'notify_url'	=> 'https://'.$_SERVER['HTTP_HOST'].'/api/pay/notify',  //支付成功回调地址
            'trade_type'	=> 'JSAPI',
            'openid'		=> $openid
        );
        $unifiedorder['sign'] = self::makeSign($unifiedorder);

        //请求数据
        $xmldata = self::array2xml($unifiedorder);
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $res = self::curl_post_ssl($url, $xmldata);
        if(!$res){
            //self::return_err("Can't connect the server");
            return response()->json(err("Can't connect the server"));
        }
        // 这句file_put_contents是用来查看服务器返回的结果 测试完可以删除了
        //file_put_contents(APP_ROOT.'/Statics/log1.txt',$res,FILE_APPEND);

        $content = self::xml2array($res);
        if(isset($content['result_code']) && strval($content['result_code']) == 'FAIL'){
            // self::return_err(strval($content['err_code_des']));
            return response()->json(err(strval($content['err_code_des'])));
        }
        if(isset($content['return_code']) && strval($content['return_code']) == 'FAIL'){
            //self::return_err(strval($content['return_msg']));
            return response()->json(err(strval($content['return_msg'])));
        }

        //self::return_data(array('data'=>$content));
        //$this->ajaxReturn($content);
        //return response()->json(succ($content));



        /**
         * 组装预支付所需要的参数
         * @param string $prepay_id 预支付ID(调用prepay()方法之后的返回数据中获取)
         * @return  json的数据
         */
        $prepay_id = $content['prepay_id'];

        $data = array(
            'appId'		=> $config['appid'],
            'timeStamp'	=> time(),
            'nonceStr'	=> self::getNonceStr(),
            'package'	=> 'prepay_id='.$prepay_id,
            'signType'	=> 'MD5'
        );

        $data['paySign'] = self::makeSign($data);

        return response()->json(succ($data));
    }

    //微信支付回调验证
    public function notify(){
        /*
         * // postman 测试数据
         <xml><appid><![CDATA[wx500ad7753d529824]]></appid>
                <bank_type><![CDATA[CFT]]></bank_type>
                <cash_fee><![CDATA[1]]></cash_fee>
                <fee_type><![CDATA[CNY]]></fee_type>
                <is_subscribe><![CDATA[N]]></is_subscribe>
                <mch_id><![CDATA[1501384751]]></mch_id>
                <nonce_str><![CDATA[078lwdwdo5q5vc99xsesy23svd9ktf2s]]></nonce_str>
                <openid><![CDATA[olELb4v3GdfgnN6s9EiTvnA2w1js]]></openid>
                <out_trade_no><![CDATA[2018042653579810]]></out_trade_no>
                <result_code><![CDATA[SUCCESS]]></result_code>
                <return_code><![CDATA[SUCCESS]]></return_code>
                <sign><![CDATA[01AAC9FA13FD9D4FC376978999F82DEF]]></sign>
                <time_end><![CDATA[20180426101105]]></time_end>
                <total_fee>1</total_fee>
                <trade_type><![CDATA[JSAPI]]></trade_type>
                <transaction_id><![CDATA[4200000076201804265875628837]]></transaction_id>
                </xml>
         */
        $xml =  isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        //var_dump($xml);die;

        // 这句file_put_contents是用来查看服务器返回的XML数据 测试完可以删除了
        //file_put_contents(APP_ROOT.'/Statics/log2.txt',$xml,FILE_APPEND);
        Log::info($xml);

        //将服务器返回的XML数据转化为数组
        $data = self::xml2array($xml);
        // 保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        // sign不参与签名算法
        unset($data['sign']);
        $sign = self::makeSign($data);

        // 判断签名是否正确  判断支付状态
        if ( ($sign===$data_sign) && ($data['return_code']=='SUCCESS') && ($data['result_code']=='SUCCESS') ) {
            $result = $data;
            //获取服务器返回的数据
            $appid = $data['appid'];   // 对应的应用的appid
            $order_no = $data['out_trade_no'];			//订单单号
            $openid = $data['openid'];					//付款人openID
            $total_fee = $data['total_fee'];			//付款金额
            $transaction_id = $data['transaction_id']; 	//微信支付流水号
            $time =substr($data['time_end'],0,4).'-'.substr($data['time_end'],4,2) .'-'.substr($data['time_end'],6,2).' '.substr($data['time_end'],8,2).':'.substr($data['time_end'],10,2).':'.substr($data['time_end'],12,2); 	//付款时间
            $mch_id = $data['mch_id'];
            //更新数据库
            // return $time;

            $order_data = [
                'notify_appid'=>$appid,
                'notify_openid' => $openid,
                'notify_total_fee' => $total_fee,
                'notify_transaction_id' => $transaction_id,
                'notify_time_end' => $time,
                'notify_mch_id' => $mch_id,
                'update_time' => date('Y-m-d H:i:s'),
                'status' => 1   //修改订单状态
            ];

            $where = ['order_no' => $order_no];
            $res = DB::table('order')->where($where)->update($order_data);  //更新订单状态

            // 如果订单信息修改失败
            if($res){
               
            }else{
                
            }

        } else {
            $result = false;
        }
        // 返回状态给微信服务器
        if ($result) {
            $str = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        } else {
            $str = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
        // return $result;
    }

//---------------------------------------------------------------用到的函数------------------------------------------------------------



    /**
     * 将一个数组转换为 XML 结构的字符串
     * @param array $arr 要转换的数组
     * @param int $level 节点层级, 1 为 Root.
     * @return string XML 结构的字符串
     */
    protected function array2xml($arr, $level = 1) {
        $s = $level == 1 ? "<xml>" : '';
        foreach($arr as $tagname => $value) {
            if (is_numeric($tagname)) {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if(!is_array($value)) {
                $s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
            } else {
                $s .= "<{$tagname}>" . $this->array2xml($value, $level + 1)."</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s."</xml>" : $s;
    }

    /**
     * 将xml转为array
     * @param  string 	$xml xml字符串
     * @return array    转换得到的数组
     */
    protected function xml2array($xml){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }

    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    protected function getNonceStr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * 生成签名
     * @return 签名
     */
    protected function makeSign($data){
        //获取微信支付秘钥
        $key = $this->config['pay_apikey'];
        // 去空
        $data=array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);
        //签名步骤二：在string后加入KEY
        //$config=$this->config;
        $string_sign_temp=$string_a."&key=".$key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }

    /**
     * 微信支付发起请求
     */
    protected function curl_post_ssl($url, $xmldata, $second=30,$aHeader=array()){
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);


        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xmldata);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
}
