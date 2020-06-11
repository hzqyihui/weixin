<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Jys_excel.php
 *
 *   Description: 表格类
 *
 *       Created: 2016/11/22 21:24:20
 *
 *        Author: huazhiqiang
 *
 * =========================================================
 */
require_once FCPATH."application/third_party/phpexcel/PHPExcel.php";
class Jys_excel{

    /**
     * 导出表格
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_order_list($header, $data, $file_name = "Jys_express_company") {
        ob_end_clean();
        ob_start();
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }

        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2+$i;
                for ($j = 0; $j < count($data[$i]); $j++) {
                    $column = chr(ord('A')+$j);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($column.$row, $data[$i][$j], PHPExcel_Cell_DataType::TYPE_STRING);
                }
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        if (empty($file_name)) {
            header('Content-Disposition:attachment;filename="Jys_express_company.xlsx"');
        }else {
            header('Content-Disposition:attachment;filename="'.$file_name.'.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导出报告模板表格
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_report_template($header, $data, $title,$file_name = "Jys_express_company") {
        ob_end_clean();
        ob_start();
//        error_reporting(0);
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }

        $file_name = date("YmdHis",time());
        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $header[$i]);
            $objPHPExcel->getActiveSheet()->setTitle($title);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2+$i;
                for ($j = 0; $j < count($data[$i]); $j++) {
                    $column = chr(ord('A')+$j);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($column.$row, $data[$i][$j], PHPExcel_Cell_DataType::TYPE_STRING);
                }
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            //$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth();
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl;charset=utf-8");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        if (empty($file_name)) {
            header('Content-Disposition:attachment;filename="Jys_express_company.xlsx"');
        }else {
            header('Content-Disposition:attachment;filename="'.$file_name.'.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');die;
    }

    /**
     * 导入excel文件来获取表格中数据
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_excel($file_name = "Jys_express_company.xlsx"){
        if (empty($file_name)) {
            return FALSE;
        }
        //判断导入表格后缀格式
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($extension == 'xlsx') {
            $objReader =PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel =$objReader->load($file_name, $encode = 'gb2312');
        } else if ($extension == 'xls'){
            $objReader =PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel =$objReader->load($file_name, $encode = 'gb2312');
        }
        $sheet =$objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();      //取得总行数
        for ($i = 2,$j = 0; $i <= $highestRow; $i++, $j++) { //除掉一二行
            $data[$j]['name'] =$objPHPExcel->getActiveSheet()->getCell("A" .$i)->getValue();
            $data[$j]['code'] =$objPHPExcel->getActiveSheet()->getCell("B" .$i)->getValue();
            $data[$j]['trajectory_query'] =$objPHPExcel->getActiveSheet()->getCell("C" .$i)->getValue();
            $data[$j]['electronic_delivery'] =$objPHPExcel->getActiveSheet()->getCell("D" .$i)->getValue();
            $data[$j]['visiting_service'] =$objPHPExcel->getActiveSheet()->getCell("E" .$i)->getValue();
            $data[$j]['create_time'] = date('Y-m-d H:i:s'); //创建时间
        }
        return $data; 

    }

    public function export_user_data_excel($file_name = NULL){
        if (empty($file_name)) {
            return FALSE;
        }
        //判断导入表格后缀格式
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($extension == 'xlsx') {
            $objReader =PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel =$objReader->load($file_name, $encode = 'gb2312');
        } else if ($extension == 'xls'){
            $objReader =PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel =$objReader->load($file_name, $encode = 'gb2312');
        }
        $sheet =$objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();      //取得总行数
        for ($i = 2,$j = 0; $i <= $highestRow; $i++, $j++) { //除掉一二行
            $data[$j]['username'] =$objPHPExcel->getActiveSheet()->getCell("A" .$i)->getValue();
            $data[$j]['name'] =$objPHPExcel->getActiveSheet()->getCell("B" .$i)->getValue();
            $data[$j]['gender'] =$objPHPExcel->getActiveSheet()->getCell("C" .$i)->getValue();
            $data[$j]['phone'] =$objPHPExcel->getActiveSheet()->getCell("D" .$i)->getValue();
            $pwd = substr($data[$j]['phone'], 5, 6);
            $data[$j]['password'] = password_hash($pwd, PASSWORD_DEFAULT);
            $data[$j]['role_id'] = 10;
            $data[$j]['create_time'] = date('Y-m-d H:i:s'); //创建时间
            $data[$j]['update_time'] = date('Y-m-d H:i:s');
        }
        return $data;

    }

    /**
     * 导入模板文件
     * @param null $file_name
     * @param $sheet_name
     * @return bool
     */
    public function import_template($file_name = NULL, &$sheet_name){
        if (empty($file_name)) {
            return FALSE;
        }
        //判断导入表格后缀格式
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($extension == 'xlsx') {
            $objReader =PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel =$objReader->load($file_name, $encode = 'gb2312');
        } else if ($extension == 'xls'){
            $objReader =PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel =$objReader->load($file_name, $encode = 'gb2312');
        }
        $sheet =$objPHPExcel->getSheet(0);
        $sheet_name =$objPHPExcel->getSheetNames();
        $highestRow = $sheet->getHighestRow();      //取得总行数
        for ($i = 2,$j = 0; $i <= $highestRow; $i++, $j++) { //除掉一二行
            $data[$j]['number'] =$objPHPExcel->getActiveSheet()->getCell("A" .$i)->getValue();
        }
        return $data;

    }

    /**
     * 导出表格
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_orders_info_list($header, $data, $file_name = "order_table") {
        ob_end_clean();
        ob_start();
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }

        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2+$i;
                $item = $data[$i];

                // 订单编号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$row, $item['order_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 子订单编号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$row, $item['order_commodity_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 商品名称
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$row, $item['commodity_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 购买数量
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$row, $item['amount'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 订单状态
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$row, $item['order_status_type'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 收件人姓名
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$row, $item['name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 收件人手机号码
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$row, $item['phone'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 收件人地址
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H'.$row, $item['address'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 快递公司名称
                if (!empty($item['express_company_name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('I'.$row, $item['express_company_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 快递单号
                if (!empty($item['express_number'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('J'.$row, $item['express_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 下单终端
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('K'.$row, $item['terminal_type'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 支付方式
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('L'.$row, $item['payment_type'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 代理商下单
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('M'.$row, $item['agent'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 下单时间
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('N'.$row, $item['create_time'], PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        if (empty($file_name)) {
            header('Content-Disposition:attachment;filename="Jys_express_company.xlsx"');
        }else {
            header('Content-Disposition:attachment;filename="'.$file_name.'.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导出表格
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_reports_info_list($header, $data, $file_name = "report_table") {
        ob_end_clean();
        ob_start();
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }

            $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2+$i;
                $item = $data[$i];

                // 样本码
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $item['number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 检测人
                if (!empty($item['name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $item['name'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 身份证号
                if (!empty($item['name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['identity_card'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 性别
                if (!empty($item['gender'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $item['gender'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 出生日期
                if (!empty($item['birth'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $item['birth'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 是否吸烟
                if (!empty($item['smoking'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $item['smoking'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 身高
                if (!empty($item['height'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $item['height'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 体重
                if (!empty($item['weight'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $item['weight'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 联系方式
                if (!empty($item['phone'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $item['phone'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 联系地址
                if (!empty($item['address'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('J'.$row, $item['addr'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 血缘关系
//                if (!empty($item['blood_relationship_name'])) {
//                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('J'.$row, $item['blood_relationship_name'], PHPExcel_Cell_DataType::TYPE_STRING);
//                }
                // 商品名称
                if (!empty($item['commodity_name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, $item['commodity_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 检测项目
                if (isset($item['project_list']) && is_array($item['project_list']) && !empty($item['project_list'])) {
                    $project_text = "";
                    foreach ($item['project_list'] as $pro) {
                        $project_text .= $pro['name'].",";
                    }
                    if (strlen($project_text) > 0) {
                        $project_text = substr($project_text, 0, strlen($project_text) - 1);
                    }
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $row, $project_text, PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 下单时间
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('M'.$row, $item['order_create_time'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 报告状态
                if (intval($item['attachment_id']) > 0) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('N' . $row, '已上传', PHPExcel_Cell_DataType::TYPE_STRING);
                }else {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('N' . $row, '未上传', PHPExcel_Cell_DataType::TYPE_STRING);
                }
                //报告类型
                if ($item['terminal_type'] == Jys_system_code::TERMINAL_TYPE_LINE){
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('O' . $row, '线下报告', PHPExcel_Cell_DataType::TYPE_STRING);
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('O' . $row, '线上报告', PHPExcel_Cell_DataType::TYPE_STRING);
                }
                //订单编号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('P' . $row, $item['order_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                //子订单编号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('Q' . $row, $item['order_commodity_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                //收件人姓名
                //收件人手机号
                if (!empty($item['order_address'])){
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('R' . $row, json_decode($item['order_address'])->name, PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('S' . $row, json_decode($item['order_address'])->phone, PHPExcel_Cell_DataType::TYPE_STRING);
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('R' . $row, $item['order_address'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('S' . $row, $item['order_address'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                //快递公司
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('T' . $row, $item['express_company_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                //快递编号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('U' . $row, $item['express_number'], PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("$column")->setAutoSize(true);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        if (empty($file_name)) {
            header('Content-Disposition:attachment;filename="report_table.xlsx"');
        }else {
            header('Content-Disposition:attachment;filename="'.$file_name.'.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * @creator huazq
     * @data 2017/8/9
     * @desc 数据导出到excel(csv文件)
     * @param $filename 导出的csv文件名称 如date("Y年m月j日").'-PB机构列表.csv'
     * @param array $tileArray 所有列名称
     * @param array $dataArray 所有列数据
     */
    public static function export_report_list_to_csv($tileArray=[], $dataArray=[], $filename){
        ob_end_clean();
        ob_start();
//        header("Content-Type: text/csv");
//        header("Content-Disposition:filename=".$filename.'.csv');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel; charset=UTF8");
        header("Content-Disposition: attachment; filename={$filename}.csv");
        $fp=fopen('php://output','w');
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        //$fp=fopen('D://hello.csv','w');
        fputcsv($fp,$tileArray);
        $index = 0;
        $i = 0;
        foreach ($dataArray as  $item) {
            // 样本码
            $temp[$i++] = $item['number'];
            // 检测人
            $temp[$i++] = $item['name'];
            // 身份证号
            $temp[$i++] = $item['identity_card'];
            // 性别
            $temp[$i++] = $item['gender'];
            // 出生日期
            $temp[$i++] = $item['birth'];
            // 是否吸烟
            $temp[$i++] = $item['smoking'];
            // 身高
            $temp[$i++] = $item['height'];
            // 体重
            $temp[$i++] = $item['weight'];
            // 联系方式
            $temp[$i++] = $item['phone'];
            // 联系地址
            if (!empty($item['address'])) {
                $temp[$i++] = $item['addr'];
            }else{
                $temp[$i++] = null;
            }
            // 商品名称
            $temp[$i++] = $item['commodity_name'];
            // 检测项目
            if (isset($item['project_list']) && is_array($item['project_list']) && !empty($item['project_list'])) {
                $project_text = "";
                foreach ($item['project_list'] as $pro) {
                    $project_text .= $pro['name'].",";
                }
                if (strlen($project_text) > 0) {
                    $project_text = substr($project_text, 0, strlen($project_text) - 1);
                }
                $temp[$i++] = $project_text;
            }else{
                $temp[$i++] = null;
            }
            // 下单时间
            $temp[$i++] = $item['order_create_time'];
            // 报告状态
            if (intval($item['attachment_id']) > 0) {
                $temp[$i++] = '已上传';
            }else {
                $temp[$i++] = '未上传';
            }
            //报告类型
            if ($item['terminal_type'] == Jys_system_code::TERMINAL_TYPE_LINE){
                $temp[$i++] = '线下报告';
            }else{
                $temp[$i++] = '线上报告';
            }
            //订单编号
            $temp[$i++] = $item['order_number'];
            //子订单编号
            $temp[$i++] = $item['order_commodity_number'];
            //收件人姓名
            //收件人手机号
            if (!empty($item['order_address'])){
                $temp[$i++] = json_decode($item['order_address'])->name;
                $temp[$i++] = json_decode($item['order_address'])->phone;
            }else{
                $temp[$i++] = $item['order_address'];
                $temp[$i++] = $item['order_address'];
            }
            //快递公司
            $temp[$i++] = $item['express_company_name'];
            //快递编号
            $temp[$i++] = $item['express_number'];
            if($index==1000){
                $index=0;
                ob_flush();
                flush();
            }
            $index++;
            fputcsv($fp,$temp);
            $i = 0;
        }
        ob_flush();
        flush();
        ob_end_clean();
    }

    /**
     * 根据报告编号和备注导出报告信息到Excel表格
     * @param $header 表头
     * @param $data 数据
     * @param string $file_name 文件名
     */
    public function export_report_list_by_report_number_and_remark_to_excel($header, $data, $file_name = "report_table") {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }

        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2+$i;
                $item = $data[$i];

                // 样本码
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $item['number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 检测人
                if (!empty($item['name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $item['name'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 身份证号
                if (!empty($item['name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['identity_card'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 性别
                if (!empty($item['gender'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $item['gender'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 出生日期
                if (!empty($item['birth'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $item['birth'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 联系方式
                if (!empty($item['phone'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $item['phone'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 联系地址
                if (!empty($item['address'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$row, $item['addr'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 备注
                if (!empty($item['remark'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('H'.$row, $item['remark'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 商品名称(模版名称)
                if (!empty($item['template_name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $item['template_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 检测项目
                if (isset($item['project_list']) && is_array($item['project_list']) && !empty($item['project_list'])) {
                    $project_text = "";
                    foreach ($item['project_list'] as $pro) {
                        $project_text .= $pro['name'].",";
                    }
                    if (strlen($project_text) > 0) {
                        $project_text = substr($project_text, 0, strlen($project_text) - 1);
                    }
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $row, $project_text, PHPExcel_Cell_DataType::TYPE_STRING);
                }  
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("$column")->setAutoSize(true);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        if (empty($file_name)) {
            header('Content-Disposition:attachment;filename="report_table.xlsx"');
        }else {
            header('Content-Disposition:attachment;filename="'.$file_name.'.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * @desc 数据导出到excel(csv文件)
     * @param $filename 导出的csv文件名称 如date("Y年m月j日").'-PB机构列表.csv'
     * @param array $tileArray 所有列名称
     * @param array $dataArray 所有列数据
     */
    public static function export_report_list_by_report_number_and_remark_to_csv($tileArray=[], $dataArray=[], $filename){
        ob_end_clean();
        ob_start();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel; charset=UTF8");
        header("Content-Disposition: attachment; filename={$filename}.csv");
        $fp=fopen('php://output','w');
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($fp,$tileArray);
        $index = 0;
        $i = 0;
        foreach ($dataArray as  $item) {
            // 样本码
            $temp[$i++] = $item['number'];
            // 检测人
            $temp[$i++] = $item['name'];
            // 身份证号
            $temp[$i++] = $item['identity_card'];
            // 性别
            $temp[$i++] = $item['gender'];
            // 出生日期
            $temp[$i++] = $item['birth'];
            // 联系方式
            $temp[$i++] = $item['phone'];
            // 商品名称（模版名称）
            $temp[$i++] = $item['template_name'];
            // 检测项目
            if (isset($item['project_list']) && is_array($item['project_list']) && !empty($item['project_list'])) {
                $project_text = "";
                foreach ($item['project_list'] as $pro) {
                    $project_text .= $pro['name'].",";
                }
                if (strlen($project_text) > 0) {
                    $project_text = substr($project_text, 0, strlen($project_text) - 1);
                }
                $temp[$i++] = $project_text;
            }else{
                $temp[$i++] = null;
            }
            // 备注
            $temp[$i++] = $item['remark'];
            // 联系地址
            if (!empty($item['address'])) {
                $temp[$i++] = $item['addr'];
            }else{
                $temp[$i++] = null;
            }
            if($index==1000){
                $index=0;
                ob_flush();
                flush();
            }
            $index++;
            fputcsv($fp,$temp);
            $i = 0;
        }
        ob_flush();
        flush();
        ob_end_clean();
    }
}