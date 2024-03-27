<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(){
        return view('uploads');
    }

    public function proses_upload(Request $request){
        $this->validate($request, [
            'file' => 'required',
            'keterangan' => 'required'
        ]);

        // menyimpan data file yang diupload ke var $file
        $file = $request->file('file');
        // get nama file
        echo 'File Name : ' .$file->getClientOriginalName() . '<br>';
        // ekstensi file
        echo 'File Extention : ' . $file->getClientOriginalExtension() . '<br>';
        // file real path
        echo 'File Real Path : ' .$file->getRealPath() . '<br>';
        // file size
        echo 'File Size : ' . $file->getSize() . '<br>';
        // file mime
        echo 'File Mime Type : ' . $file->getMimeType() . '<br>';
        
        echo 'FILE BERHASIL DIUPLOAD'. '<br>';

        // save file
        $tujuanUpload = 'uploads';
        $file->move($tujuanUpload, $file->getClientOriginalName());
    }

    public function dropzone(){
        return view('dropzone');
    }

    public function dropzone_strore(Request $request){
        $image = $request->file('file');

        $imageName = time() .'.'. $image->extension();
        $image->move(public_path('img/dropzone'), $imageName);
        return response()->json(['success' => $imageName]);
    }

}
