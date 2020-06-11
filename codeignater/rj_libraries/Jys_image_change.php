<?php

/**
 *       Filename: Jys_image_change.php
 *
 *    Description: 缩略图类库
 *
 *        Created: 2017-06-19 16:43
 *
 *         Author: huazhiqiang
 */
class Jys_image_change
{
    private $_CI;

    public function __construct(){
        $this->_CI =& get_instance();
    }

    /**
     * 改变图像的比例，并返回新图像地址
     * @param null $source_path
     * @param float $scale
     * @return bool|string
     */
    public function change_image_scale($source_path = null, $scale = 1.0){
        $dictionary = 'source/new/';
        $image_info = getimagesize($source_path);
        $image_type = $image_info['mime'];
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        $new_width = $image_width * $scale;
        $new_height = $image_height * $scale;
        $new = imagecreatetruecolor($new_width, $new_height);
        $this->_make_dir($dictionary);
        switch ($image_type) {
            case 'image/gif':
                $new_path = $dictionary . time() . '.gif';
                $source_image = imagecreatefromgif($source_path);
                break;

            case 'image/jpeg':
                $new_path = $dictionary . time() . '.jpeg';
                $source_image = imagecreatefromjpeg($source_path);
                break;

            case 'image/png':
                $new_path = $dictionary . time() . '.png';
                $source_image = imagecreatefrompng($source_path);
                break;
            default:
                return false;
                break;
        }
        //copy部分图像并调整
        imagecopyresampled($new, $source_image, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
        //图像输出新图片、另存为
        switch ($image_type) {
            case 'image/gif':
                //header('Content-Type: image/gif');
                imagegif($new, $new_path);
                break;

            case 'image/jpeg':
                //header('Content-Type: image/jpeg');
                imagejpeg($new, $new_path);
                break;

            case 'image/png':
                //header('Content-Type: image/png');
                imagepng($new, $new_path);
                break;
            default:
                return false;
                break;
        }
        imagedestroy($new);
        imagedestroy($source_image);
        return FCPATH . $new_path;
    }

    /**
     * 改变图像的比例，并返回新图像地址
     * @param null $source_path
     * @param float $scale
     * @return bool|string
     */
    public function change_image_target_width_height($source_path = null, $target_width = 200, $target_height = 200)
    {
        $dictionary = 'source/new/';
        $image_info = getimagesize($source_path);
        $image_type = $image_info['mime'];
        $source_width = $image_info[0];
        $source_height = $image_info[1];
        $source_ratio = $source_height / $source_width;
        $target_ratio = $target_height / $target_width;
        // 源图过高
        if ($source_ratio > $target_ratio) {
            $cropped_width = $source_width;
            $cropped_height = $source_width * $target_ratio;
            $source_x = 0;
            $source_y = ($source_height - $cropped_height) / 2;
        } elseif ($source_ratio < $target_ratio) { // 源图过宽
            $cropped_width = $source_height / $target_ratio;
            $cropped_height = $source_height;
            $source_x = ($source_width - $cropped_width) / 2;
            $source_y = 0;
        } else { // 源图适中
            $cropped_width = $source_width;
            $cropped_height = $source_height;
            $source_x = 0;
            $source_y = 0;
        }
        $this->_make_dir($dictionary);
        switch ($image_type) {
            case 'image/gif':
                $new_path = $dictionary . time() . '.gif';
                $source_image = imagecreatefromgif($source_path);
                break;

            case 'image/jpeg':
                $new_path = $dictionary . time() . '.jpeg';
                $source_image = imagecreatefromjpeg($source_path);
                break;

            case 'image/png':
                $new_path = $dictionary . time() . '.png';
                $source_image = imagecreatefrompng($source_path);
                break;
            default:
                return false;
                break;
        }
        $target_image = imagecreatetruecolor($target_width, $target_height);
        $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);
        // 裁剪
        imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
        //copy部分图像并调整
        imagecopyresampled($target_image, $source_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
        //图像输出新图片、另存为
        switch ($image_type) {
            case 'image/gif':
                //header('Content-Type: image/gif');
                imagegif($target_image, $new_path);
                break;

            case 'image/jpeg':
                //header('Content-Type: image/jpeg');
                imagejpeg($target_image, $new_path);
                break;

            case 'image/png':
                //header('Content-Type: image/png');
                imagepng($target_image, $new_path);
                break;
            default:
                return false;
                break;
        }
        imagedestroy($target_image);
        imagedestroy($source_image);
        imagedestroy($cropped_image);
        return FCPATH . $new_path;
    }

    /**
     * 检验目录是否存在，不存在则新建
     * @param  string $dir 目录路径
     */
    private function _make_dir($dir)
    {
        $dir = FCPATH . $dir;
        if (!is_dir($dir)) {
            $res = mkdir($dir, 0755, true);
        }
    }
}