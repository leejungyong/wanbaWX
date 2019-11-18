<?php


/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('PRC');

// require '../../../default/d/source/class/class_core.php';
// $discuz = &discuz_core::instance();
// $discuz->init();
 $arr = json_decode(file_get_contents("php://input"), true);
$data=$arr['data'];
/** 引入PHPExcel */
//require_once dirname(__FILE__) . './Classes/PHPExcel.php';
require_once dirname(__FILE__) . '/excel/PHPExcel.php';
// 创建Excel文件对象
$objPHPExcel = new PHPExcel();
// 设置文档信息，这个文档信息windows系统可以右键文件属性查看
// $objPHPExcel->getProperties()->setCreator("作者简庆旺")
//     ->setLastModifiedBy("最后更改者")
//     ->setTitle("文档标题")
//     ->setSubject("文档主题")
//     ->setDescription("文档的描述信息")
//     ->setKeywords("设置文档关键词")
//     ->setCategory("设置文档的分类");


//根据excel坐标，添加数据
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', '点位名称')
    ->setCellValue('B1', '点位描述')
    ->setCellValue('C1', '点位地址')
    ->setCellValue('D1', '腾讯地图坐标')
    ->setCellValue('E1', '原始GPS坐标')
    ->setCellValue('F1', '分类');

    foreach($data as $k=>$v){
        $index=$k+2;
        $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$index, $v['name'])
    ->setCellValue('B'.$index, $v['pmemo'])
    ->setCellValue('C'.$index, $v['address'])
    ->setCellValue('D'.$index, $v['latlng'])
    ->setCellValue('E'.$index, $v['poi'])
    ->setCellValue('F'.$index, $v['cat']);
    }

// 混杂各种符号, 编码为UTF-8
// $objPHPExcel->setActiveSheetIndex(0)
//     ->setCellValue('A4', 'Miscellaneous glyphs')
//     ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');


// $objPHPExcel->getActiveSheet()->setCellValue('A8',"你好世界");
// $objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
// $objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);


// $value = "-ValueA\n-Value B\n-Value C";
// $objPHPExcel->getActiveSheet()->setCellValue('A10', $value);
// $objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(-1);
// $objPHPExcel->getActiveSheet()->getStyle('A10')->getAlignment()->setWrapText(true);
// $objPHPExcel->getActiveSheet()->getStyle('A10')->setQuotePrefix(true);


// 重命名工作sheet
$objPHPExcel->getActiveSheet()->setTitle('点位');

// 设置第一个sheet为工作的sheet
$objPHPExcel->setActiveSheetIndex(0);
$rnd = mt_rand();
$fm = md5(time() . $rnd);
// 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save( 'export/'.$fm.'.xlsx');


// 保存Excel 95格式文件，，保存路径为当前路径，
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('export/'.$fm.'.xls');