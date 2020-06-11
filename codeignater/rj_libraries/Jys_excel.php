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
require_once FCPATH . "application/third_party/phpexcel/PHPExcel.php";

class Jys_excel
{

    /**
     * 导出表格
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_order_list($header, $data, $file_name = "Jys_express_company")
    {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }

        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2 + $i;
                for ($j = 0; $j < count($data[$i]); $j++) {
                    $column = chr(ord('A') + $j);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($column . $row, $data[$i][$j], PHPExcel_Cell_DataType::TYPE_STRING);
                }
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
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
        } else {
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导入excel文件来获取表格中数据
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_excel($file_name = "Jys_express_company.xlsx")
    {
        if (empty($file_name)) {
            return FALSE;
        }
        //判断导入表格后缀格式
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($extension == 'xlsx') {
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($file_name, $encode = 'gb2312');
        } else if ($extension == 'xls') {
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($file_name, $encode = 'gb2312');
        }
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();      //取得总行数
        for ($i = 2, $j = 0; $i <= $highestRow; $i++, $j++) { //除掉一二行
            $data[$j]['name'] = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
            $data[$j]['code'] = $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();
            $data[$j]['trajectory_query'] = $objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue();
            $data[$j]['electronic_delivery'] = $objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue();
            $data[$j]['visiting_service'] = $objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue();
            $data[$j]['create_time'] = date('Y-m-d H:i:s'); //创建时间
        }
        return $data;

    }

    public function export_user_data_excel($file_name = NULL)
    {
        if (empty($file_name)) {
            return FALSE;
        }
        //判断导入表格后缀格式
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($extension == 'xlsx') {
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($file_name, $encode = 'gb2312');
        } else if ($extension == 'xls') {
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($file_name, $encode = 'gb2312');
        }
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();      //取得总行数
        for ($i = 2, $j = 0; $i <= $highestRow; $i++, $j++) { //除掉一二行
            $data[$j]['username'] = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
            $data[$j]['name'] = $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();
            $data[$j]['gender'] = $objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue();
            $data[$j]['phone'] = $objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue();
            $pwd = substr($data[$j]['phone'], 5, 6);
            $data[$j]['password'] = password_hash($pwd, PASSWORD_DEFAULT);
            $data[$j]['role_id'] = 10;
            $data[$j]['create_time'] = date('Y-m-d H:i:s'); //创建时间
            $data[$j]['update_time'] = date('Y-m-d H:i:s');
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
    public function export_orders_info_list($header, $data, $file_name = "order_table")
    {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }

        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2 + $i;
                $item = $data[$i];

                // 订单编号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $item['order_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 子订单编号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $item['order_commodity_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 商品名称
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['commodity_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 购买数量
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $item['amount'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 订单状态
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $item['order_status_type'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 收件人姓名
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $item['name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 收件人手机号码
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $item['phone'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 收件人地址
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $item['address'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 快递公司名称
                if (!empty($item['express_company_name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $item['express_company_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 快递单号
                if (!empty($item['express_number'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $row, $item['express_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 下单终端
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, $item['terminal_type'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 支付方式
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $row, $item['payment_type'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 代理商下单
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('M' . $row, $item['agent'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 下单时间
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('N' . $row, $item['create_time'], PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
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
        } else {
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导出买家信息报表
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_user_info_report($header, $data, $file_name = "user_info_report")
    {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }

        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2 + $i;
                $item = $data[$i];

                // 客户编号
                if (!empty($item['number'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $item['number'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 客户名称
                if (!empty($item['name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $item['name'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 客户等级
                if (!empty($item['level_name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['level_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 下单次数
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, intval($item['order_count']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 下单金额
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, floatval($item['total_price']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 客单量
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, intval($item['order_count']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 下单总品规数
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, intval($item['order_commodity_count']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 下单平均品规数
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, floatval($item['avg_spec_number']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 积分
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, intval($item['current_point']), PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
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
            header('Content-Disposition:attachment;filename="user_info_report.xlsx"');
        } else {
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导出品种报表
     * @param $header
     * @param $data
     * @param null $start_time
     * @param null $end_time
     * @param string $file_name
     */
    public function export_breed_report($header, $data, $start_time = NULL, $end_time = NULL, $file_name = 'breed_report')
    {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }

        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2 + $i;
                $item = $data[$i];

                // 时间段
                $time_bucket = "";
                if (!empty($start_time) && !empty($end_time)) {
                    $time_bucket = $start_time . "至" . $end_time;
                } else if (empty($start_time) && !empty($end_time)) {
                    $time_bucket = "2017-01-01 00:00:00至" . $end_time;
                } else if (!empty($start_time) && empty($end_time)) {
                    $time_bucket = $start_time . "至" . date('Y-m-d H:i:s');
                } else {
                    $time_bucket = "2017-01-01 00:00:00至" . date('Y-m-d H:i:s');
                }
                if (!empty($time_bucket)) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $time_bucket, PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 商品编号
                if (!empty($item['number'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $item['number'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 商品名称
                if (!empty($item['name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['name'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 规格
                if (!empty($item['specification'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $item['specification'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 生产厂家
                if (!empty($item['manufacturer'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $item['manufacturer'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 成交数量
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, intval($item['order_commodity_amount']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 成交金额(元)
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, floatval($item['order_commodity_total_price']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 购买会员数
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, intval($item['enterprise_count']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
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
            header('Content-Disposition:attachment;filename="breed_report.xlsx"');
        } else {
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导出优惠价信息报表
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_coupon_info_report($header, $data, $file_name = "coupon_info_report")
    {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }
        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2 + $i;
                $item = $data[$i];

                // 用户手机号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $item['phone'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 优惠卷名称
                if (!empty($item['name'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $item['name'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 满足条件
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['condition'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 减免金额
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $item['privilege'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 生效起始时间
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $item['start_time'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 生效结束时间
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $item['end_time'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 领取时间
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $item['end_time'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 状态
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $item['status_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
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
            header('Content-Disposition:attachment;filename="user_info_report.xlsx"');
        } else {
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导出优惠价信息报表
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_coupon_code_detail($header, $data, $file_name = "coupon_code_detail_")
    {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }
        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2 + $i;
                $item = $data[$i];

                // 用户手机号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $item['code'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 优惠卷名称
                if ($item['status'] == jys_system_code::COUPON_CODE_STATUS_UNEXCHANGE) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, '未兑换', PHPExcel_Cell_DataType::TYPE_STRING);
                } elseif ($item['status'] == jys_system_code::COUPON_CODE_STATUS_EXCHANGED) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, '已兑换', PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 满足条件
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['phone'], PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
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
            header('Content-Disposition:attachment;filename="user_info_report.xlsx"');
        } else {
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导出用户信息报表
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_user_report($header, $data, $file_name = "user_info_report")
    {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }
        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2 + $i;
                $item = $data[$i];

                // 用户手机号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $item['phone'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 用户微信openid
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $item['openid'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 审核状态
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['status_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 会员等级
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $item['level_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 当前积分
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $item['current_point'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 总积分
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $item['total_point'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 用户余额
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $item['balance'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 已冻结余额
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $item['frozen_balance'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 订单数
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $item['order_count'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 最后一次下单时间
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $row, $item['last_order_time'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 抽奖次数
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, $item['sweepstakes_num'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
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
            header('Content-Disposition:attachment;filename="user_info_report.xlsx"');
        } else {
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导出企业信息报表
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_enterprise_info_report($header, $data, $file_name = "enterprise_info_report")
    {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }
        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2 + $i;
                $item = $data[$i];

                // 账号手机号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $item['user_phone'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 企业名称
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $item['name'], PHPExcel_Cell_DataType::TYPE_STRING);   
                //erp企业名称
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['erp_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 企业类型
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $item['enterprise_type'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 注册地址
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $item['address'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 送货地址
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $item['erp_address'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 客服
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $item['customer_service_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 销售员
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $item['salesman_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 联系人姓名
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $item['contacts'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 联系人手机号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $row, $item['phone'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 联系人邮箱
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, $item['email'], PHPExcel_Cell_DataType::TYPE_STRING);
                // GSP状态
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $row, $item['gsp_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 注册日期
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('M' . $row, $item['create_time'], PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
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
            header('Content-Disposition:attachment;filename="user_info_report.xlsx"');
        } else {
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导出订单信息报表
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_order_info_report($header, $data, $file_name = "enterprise_info_report")
    {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }
        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2 + $i;
                $item = $data[$i];

                // 订单号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $item['number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 品种数量
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $item['order_commodity_number'], PHPExcel_Cell_DataType::TYPE_NUMERIC);  
                // 买家姓名
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['enterprise_contacts'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 单位
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $item['enterprise_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 金额
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $item['total_price'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 下单时间
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $item['create_time'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 状态
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $item['order_status_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 客服员
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $item['customer_username'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 销售员
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $item['salesman_username'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 付款方式
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $row, $item['payment_type_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 是否要发票
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, '是', PHPExcel_Cell_DataType::TYPE_STRING);
                // 收货地址
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $row, $item['address'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 备注
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('M' . $row, $item['message'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 出库日期
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('N' . $row, $item['delivered_time'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 出库金额
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('O' . $row, $item['ship_price'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 实付金额
                if ($item['status_id'] == 100) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('P' . $row, 0, PHPExcel_Cell_DataType::TYPE_STRING);
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('P' . $row, $item['payment_amount'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 对方业务员
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('Q' . $row, $item['enterprise_contacts'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 单位编号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('R' . $row, $item['business_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 支付方式
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('S' . $row, $item['payment_type_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 余额金额
                // $objPHPExcel->getActiveSheet()->setCellValueExplicit('T' . $row, $item['balance_pay_amount'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 优惠金额
                // $objPHPExcel->getActiveSheet()->setCellValueExplicit('U' . $row, $item['coupon_amount'], PHPExcel_Cell_DataType::TYPE_STRING);

            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A') + $i);
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
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
            header('Content-Disposition:attachment;filename="user_info_report.xlsx"');
        } else {
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导出商品信息列表
     * @param $header
     * @param $data
     * @param string $file_name
     */
    public function export_commodity_to_excel($header, $data, $file_name = "commodity_info") {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }
        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            if ($i >= 26) {
                $column = 'A'.chr(ord('A') + ($i - 26));
            }else {
                $column = chr(ord('A') + $i);
            }

            $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2 + $i;
                $item = $data[$i];

                // 商品编号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $item['number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 商品名称
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $item['name'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
                // 拼音简码
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $item['PY_brevity_code'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 通用名
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $item['common_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 商品规格
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $item['specification'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 批号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $item['batch_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 生产日期
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $item['production_date'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 批准文号
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $item['approval_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 生产厂家
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $item['manufacturer'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 包装单位
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $row, $item['pack_units'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 效期
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, $item['expiry_date'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 现金价
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $row, $item['cash_price'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 欠款价
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('M' . $row, $item['arrears_price'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 超市价
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('N' . $row, $item['supermarket_price'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 基药价
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('O' . $row, $item['basic_medicine'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 有效期品种价格
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('P' . $row, $item['valid_variety_price'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 商品类别
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('Q' . $row, $item['type_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 剂型
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('R' . $row, $item['dosage_form_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 是否控销
                if (intval($item['control_sales']) == 0) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('S' . $row, '否', PHPExcel_Cell_DataType::TYPE_STRING);
                }else {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('S' . $row, '是', PHPExcel_Cell_DataType::TYPE_STRING);
                }
                // 是否有说明书
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('T' . $row, intval($item['instruction_book']) == 0 ? '无' : '有', PHPExcel_Cell_DataType::TYPE_STRING);
                // 药理药效
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('U' . $row, intval($item['effects']) == 0 ? '无' : '有', PHPExcel_Cell_DataType::TYPE_STRING);
                // 是否有包装盒
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('V' . $row, intval($item['package_box']) == 0 ? '无' : '有', PHPExcel_Cell_DataType::TYPE_STRING);
                // 库存
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('W' . $row, $item['store'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 销量
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('X' . $row, $item['sales_volume'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 购买获得积分
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('Y' . $row, $item['points'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 品牌
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('Z' . $row, $item['brand_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 分类
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('AA' . $row, $item['category_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 状态
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('AB' . $row, $item['status_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                // 采购员
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('AC' . $row, $item['buyer_username'], PHPExcel_Cell_DataType::TYPE_STRING);

            }
        }
        for ($i = 0; $i < count($header); $i++) {
            if ($i >= 26) {
                $column = 'A'.chr(ord('A') + ($i - 26));
            }else {
                $column = chr(ord('A') + $i);
            }
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
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
            header('Content-Disposition:attachment;filename="commodity_info.xlsx"');
        } else {
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }
}