<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once FCPATH."application/third_party/phpexcel/PHPExcel.php";
class Jibian_excel
{
    /**
     * 导出订单表格
     * @param $header 表头
     * @param $data 数据
     */
    public function export_order_list($header, $data, $file_name = "jibian_order") {
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
        header("Content-Type:application/download");;
        if (empty($file_name)) {
            header('Content-Disposition:attachment;filename="jibian_order.xlsx"');
        }else {
            header('Content-Disposition:attachment;filename="'.$file_name.'.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    private function convert_utf8($str) {
        if (empty($str))
            return "";
        return iconv('gb2312', 'utf-8//ignore', $str);
    }
}