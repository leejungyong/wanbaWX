<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
require '../../../source/class/class_core.php';
$discuz = &discuz_core::instance();
$discuz -> init();
$path='temp.xlsx';
foreach ($_FILES as $name => $file) {
	move_uploaded_file($file['tmp_name'], $path);
}
$o = importExecl($path);
if ($o[1]['A'] != '时间') {
	echo json_encode(array('status' => false, 'msg' => '表格式错误,请重新上传正确格式的excel表文件'));
} else if ($o[1]['B'] != '项目名称') {
	echo json_encode(array('status' => false, 'msg' => '表格式错误,请重新上传正确格式的excel表文件'));
} else if ($o[1]['C'] != '项目地点') {
	echo json_encode(array('status' => false, 'msg' => '表格式错误,请重新上传正确格式的excel表文件'));
} else if ($o[1]['D'] != '客户') {
	echo json_encode(array('status' => false, 'msg' => '表格式错误,请重新上传正确格式的excel表文件'));
} else if ($o[1]['E'] != '人数') {
	echo json_encode(array('status' => false, 'msg' => '表格式错误,请重新上传正确格式的excel表文件'));
} else if ($o[1]['F'] != '项目经理') {
	echo json_encode(array('status' => false, 'msg'=> '表格式错误,请重新上传正确格式的excel表文件'));
} else if ($o[1]['G'] != '执行人员') {
	echo json_encode(array('status' => false, 'msg' => '表格式错误,请重新上传正确格式的excel表文件'));
} else if ($o[1]['H'] != '金额') {
	echo json_encode(array('status' => false, 'msg' => '表格式错误,请重新上传正确格式的excel表文件'));
} else if ($o[1]['I'] != '备注') {
	echo json_encode(array('status' => false, 'msg' => '表格式错误,请重新上传正确格式的excel表文件'));
} else {
	DB::delete("project_board","1=1");
	for ($i = 2; $i <= count($o); $i++) {
		if($o[$i]['A'] && $o[$i]['B'] && $o[$i]['C'] && $o[$i]['D']){
			$data=array(
			"date"=>$o[$i]['A'],
			"name"=>$o[$i]['B'],
			"place"=>$o[$i]['C'],
			"custom"=>$o[$i]['D'],
			"num"=>$o[$i]['E'],
			"chief"=>$o[$i]['F'],
			"crews"=>$o[$i]['G'],
			"amount"=>$o[$i]['H'],
			"memo"=>$o[$i]['I']
			);
			DB::insert("project_board",$data);
		};
	}
	echo json_encode(array('status' => true, 'msg' => '数据更新成功'));
}

/**
 *  数据导入
 * @param string $file excel文件
 * @param string $sheet
 * @return string   返回解析数据
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 */
function importExecl($file = '', $sheet = 0) {
	$file = iconv("utf-8", "gb2312", $file);
	//转码
	if (empty($file) OR !file_exists($file)) {
		die('file not exists!');
	}
	include ('PHPExcel.php');
	//引入PHP EXCEL类
	$objRead = new PHPExcel_Reader_Excel2007();
	//建立reader对象
	if (!$objRead -> canRead($file)) {
		$objRead = new PHPExcel_Reader_Excel5();
		if (!$objRead -> canRead($file)) {
			die('No Excel!');
		}
	}

	$cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

	$obj = $objRead -> load($file);
	//建立excel对象
	$currSheet = $obj -> getSheet($sheet);
	//获取指定的sheet表
	$columnH = $currSheet -> getHighestColumn();
	//取得最大的列号
	$columnCnt = array_search($columnH, $cellName);
	$rowCnt = $currSheet -> getHighestRow();
	//获取总行数

	$data = array();
	for ($_row = 1; $_row <= $rowCnt; $_row++) {//读取内容
		for ($_column = 0; $_column <= $columnCnt; $_column++) {
			$cellId = $cellName[$_column] . $_row;
			$cellValue = $currSheet -> getCell($cellId) -> getValue();
			//$cellValue = $currSheet->getCell($cellId)->getCalculatedValue();  #获取公式计算的值
			if ($cellValue instanceof PHPExcel_RichText) {//富文本转换字符串
				$cellValue = $cellValue -> __toString();
			}

			$data[$_row][$cellName[$_column]] = $cellValue;
		}
	}

	return $data;
}

?>

