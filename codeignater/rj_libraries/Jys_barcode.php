<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once FCPATH."application/third_party/phpbarcode/class/BCGColor.php";
require_once FCPATH."application/third_party/phpbarcode/class/BCGDrawing.php";
require_once FCPATH."application/third_party/phpbarcode/class/BCGcode128.barcode.php";

class Jys_barcode {

    /*
     * 生成条形码
     * @param $text 报告编号
     */
    public function create_barcode($number, $path = '', $filename = "")
    {
        if (empty($number)) {
            return FALSE;
        }
        $colorFront = new BCGColor(0, 0, 0);
        $colorBack  = new BCGColor(255, 255, 255);

        // Barcode Part
        $code = new BCGcode128();
        $code->setScale(2);
        $code->setColor($colorFront, $colorBack);
        $code->parse($number);

        if (empty($path)) {
            $path = FCPATH."/source/download/barcode/";
        }
        if (!file_exists($path)) {
            if (!mkdir($path, 0777, TRUE)) {
                return FALSE;
            }
        }

        // Drawing Part
        if (empty($filename)) {
            $drawing = new BCGDrawing($path.$number.'.png', $colorBack);
        }else {
            $drawing = new BCGDrawing($path.$filename.'.png', $colorBack);
        }

        $drawing->setBarcode($code);
        $drawing->draw();

        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
        
        return TRUE;
    }
}
