<?php
/**
 * =====================================================================================
 *
 *        Filename: Lab_attachment.php
 *
 *     Description: 附件类库
 *
 *         Created: 2017-03-16 19:25:18
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
namespace App\Libraries;

class Lab_attachment{
    public function up_attachment($request, $dir = '/public/uploads'){
        if($request->hasFile('file')){
            $data = [];
            $file = $request->file('file');
            $this->_make_dir($dir);  //创建文件夹
            $clientName = $file->getClientOriginalName();
            //获取tmp文件名
            $tempName = $file->getFilename();
            //上传文件的tmp文件所在的路径 例如：php\xampp\php\tmp\phpCB22.tmp
            $realPath = $file->getRealPath();
            //文件后缀
            $extension = $file->getClientOriginalExtension();
            $mimetype = $file->getClientMimeType();
            $newName  = date('YmdHis').'.'.$extension;
            $file->move(base_path($dir),$newName);
            $path = 'uploads/'.$newName;

            $data['md5'] = md5_file($path);
            $data['name'] = $newName;
            $data['path'] = $path;
            $data['success'] = TRUE;
            return $data;
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '请检查是否上传文件';
            return $data;
        }
    }

    /**
     * 检验目录是否存在，不存在则新建
     * @param  string $dir 目录路径
     */
    private function _make_dir($dir){
        $dir = base_path().$dir;
        if (!is_dir($dir)){
            $res = mkdir($dir, 0755, true);
        }
    }

}