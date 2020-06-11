<?php
/**
 * =====================================================================================
 *
 *        Filename: Lab_excel.php
 *
 *     Description: 表格类库
 *
 *         Created: 2017-05-09 16:16:37
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
namespace App\Libraries;

require_once app_path."/Thirdparty/phpexcel/PHPExcel.php";
class Lab_excel{
    /**
     * 导出表格
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_order_list($header, $data, $file_name = "lab") {
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

    private function convert_utf8($str) {
        if (empty($str))
            return "";
        return iconv('gb2312', 'utf-8//ignore', $str);
    }

    /**
     * 导入excel文件来获取表格中数据
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_excel($file_name = "lab.xlsx"){
        if (empty($file_name)) {
            return FALSE;
        }
        //判断导入表格后缀格式
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($extension == 'xlsx') {
            $objReader =PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel =$objReader->load($file_name, $encode = 'utf-8');
        } else if ($extension == 'xls'){
            $objReader =PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel =$objReader->load($file_name, $encode = 'utf-8');
        }
        $sheet =$objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();      //取得总行数
        //$highestColumn =$sheet->getHighestColumn(); //取得总列数
        for ($i = 2,$j = 0; $i <= $highestRow; $i++, $j++) { //除掉一二行
            $data[$j]['name'] =$objPHPExcel->getActiveSheet()->getCell("A" .$i)->getValue();
            $data[$j]['code'] =$objPHPExcel->getActiveSheet()->getCell("B" .$i)->getValue();
            $data[$j]['create_time'] = date('Y-m-d H:i:s'); //创建时间
        }
        return $data;
    }
}