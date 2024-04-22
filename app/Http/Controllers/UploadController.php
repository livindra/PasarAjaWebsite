<?php

namespace  App\Http\Controllers;
use App\Models\Upload;
use Illuminate\Http\Request;

class UploadController extends Controller{
    public function index() {

        return view('dropzone');
    }
    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'type' => 'required|min:5',
            'price' => 'required|min:5',
            'sparepart' => 'required|min:5',
            'qty' => 'required',
            'file' => 'mimes:png,jpg,jpeg,gif|max:5000'
        ]);

        // Simpan data pada tabel services
        $service = Upload::create([  //Service = model
            'tipe_service' => $request->type,
            'price' => $request->price,
            'sparepart' => $request->sparepart,
            'qty' => $request->qty,
            'file' => ''
        ]);

        // get dropzone image
        if ($request->file('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('img/dropzone', $filename, 'public'); // Simpan file pada direktori img/dropzone
            $service->update([
                'file' => '/storage/img/dropzone/' . $filename // Simpan path file dalam database
            ]);
        }

        // Simpan data pada tabel reports
       
        // Redirect ke halaman tertentu setelah data berhasil ditambahkan
        return redirect('/service-index')->with('success', 'Data service berhasil ditambahkan');
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
}