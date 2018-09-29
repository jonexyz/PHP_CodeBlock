<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-09-29
 * Time: 9:15
 */

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

//设置工作表标题名称
$worksheet->setTitle('宝塔响应日志统计表');


$file = fopen("www.domain.com.log", "r");

$i=3;
//输出文本中所有的行，直到文件结束为止。
while(! feof($file))
{
    $row = fgets($file);

    $index0= stripos($row,'- -');
    $ip = trim(substr($row,0,$index0));

    $index1= stripos($row,'[');
    $index2= stripos($row,']');
    $leng = $index2-$index1;
    $time = trim(substr($row,$index1+1,$leng));  // 请求时间

    $index3 = stripos($row,'"');
    $index4 = stripos($row,'"-"');
    $leng = $index4-$index3;
    $method = trim(substr($row,$index3+1,$leng));  // 请求方法


    $status_code = '状态码';  // 状态码


    $user_gent = trim(substr($row,$index3+3));  // User Agent

    $worksheet->setCellValueByColumnAndRow(1, $i, $ip);
    $worksheet->setCellValueByColumnAndRow(2, $i, $time);
    $worksheet->setCellValueByColumnAndRow(3, $i, $method);
    $worksheet->setCellValueByColumnAndRow(4, $i, $status_code);
    $worksheet->setCellValueByColumnAndRow(5, $i, $user_gent);

    $i++;
}
fclose($file);


//表头
//设置单元格内容
$worksheet->setCellValueByColumnAndRow(1, 1, '宝塔响应日志统计表'); //  第1列  第1行
$worksheet->setCellValueByColumnAndRow(1, 2, 'IP');      // 第1列  第二行
$worksheet->setCellValueByColumnAndRow(2, 2, '请求时间');     // 第2列  第二行
$worksheet->setCellValueByColumnAndRow(3, 2, '请求方法');     // 第3列  第二行
$worksheet->setCellValueByColumnAndRow(4, 2, '状态码');     // 第5列  第二行
$worksheet->setCellValueByColumnAndRow(5, 2, 'User Agent');     // 第5列  第二行

//合并单元格
$worksheet->mergeCells('A1:G1');

// 定义单元格样式
$styleArray = [
    'font' => [
        'bold' => true
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    ],
];

//设置单元格样式
$worksheet->getStyle('A1')->applyFromArray($styleArray)->getFont()->setSize(28);

$worksheet->getStyle('A2:E2')->applyFromArray($styleArray)->getFont()->setSize(14);


// 保存到test.xlsx 文件中
$writer = new Xlsx($spreadsheet);
$writer->save('test.xlsx');


/*$file = fopen("www.monopo.cn.log", "r");
$user=array();
$i=0;
//输出文本中所有的行，直到文件结束为止。
while(! feof($file))
{
    $user[$i]= substr(fgets($file),0,14);//fgets()函数从文件指针中读取一行
    $i++;
}
fclose($file);
$user=array_filter($user);

foreach ($user as $key=>$value){

    if(isset($arr[trim($value)])){
        $arr[trim($value)] = $arr[trim($value)]+1;
    }else{
        $arr[trim($value)] = 0;
    }
}

foreach ($arr as $k=>$v){
    $test[$v]=$k;
}
krsort($test);
print_r($test);*/