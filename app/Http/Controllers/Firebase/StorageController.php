<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Exception\FirebaseException;

class StorageController extends Controller
{

    public function uploadImage(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'storage' => 'required',
            'is_update' => 'nullable|bool',
            'document_id' => 'nullable'
        ], [
            'image.required' => 'Gambar wajib diunggah.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan adalah jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 2048 kb.',
            'storage.required' => 'Storage path harus diisi'
        ]);

        // Cek jika validasi gagal
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 400);
        }

        $image = $request->file('image');
        $storagePath = $request->input('storage');
        $isUpdate = $request->input('is_update');
        $documentId = $request->input('document_id');

        // membuat filename
        $name = time();
        $extension = $image->getClientOriginalExtension();
        $file =  $name . '.' . $extension;

        // memindahkan gambar ke local (temp)
        if (app()->environment('local')) {
            $localfolder = public_path('temp/');
        } else {
            $localfolder = public_path(base_path('../public_html/public/temp/'));
        }

        if (!is_dir($localfolder)) {
            mkdir($localfolder, 0777, true);
        }

        if ($image->move($localfolder, $file)) {
            // upload gambar ke firebase storage
            $firebaseStorage = app('firebase.storage');
            $bucket = $firebaseStorage->getBucket();
            $bucket->upload(fopen($localfolder . '/' . $file, 'r'), ['name' => $storagePath . '/' . $file]);

            if (!$isUpdate) {
                // simpan nama gambar di firestore
                $firestore = app('firebase.firestore')
                    ->database()
                    ->collection('Images')
                    ->newDocument();
                $firestore->set([
                    'image_url' => $storagePath . '/' . $file,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                // update
                $firestore = app('firebase.firestore')
                    ->database()
                    ->collection('Images')
                    ->document($documentId);
                $firestore->set([
                    'image_url' => $storagePath . '/' . $file,
                    'updated_at' => Carbon::now(),
                ], ['merge' => true]);
            }


            // hapus temporary file
            unlink($localfolder . '/' . $file);

            // get path gambar
            $imageUrl = $bucket->object($storagePath . $name)->signedUrl(new \DateTime('+1 week'));

            return response()->json(['status' => 'success', 'message' => 'berhasil upload gambar', 'download_link' => $imageUrl], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'gagal upload gambar'], 400);
    }

    public function deleteImage(Request $request)
    {
        try {
            // Get the filename to delete from the request
            $filename = $request->input('filename');

            // Delete the image from Firebase Storage
            $firebaseStorage = app('firebase.storage');
            $firebaseStorage->getBucket()->object($filename)->delete();

            return response()->json(['status' => 'success', 'message' => 'Image deleted successfully'], 200);
        } catch (\Exception $e) {
            // Return error response if deletion fails
            return response()->json(['status' => 'error', 'message' => 'Failed to delete image: ' . $e->getMessage()], 500);
        }
    }

    public function updateImage(Request $request)
    {
        $image = $request->file('image');
        $storagePath = $request->input('storage');
        $oldFilename = $request->input('old_filename');
        $documentId = $request->input('document_id');

        $request->merge(['filename' => $oldFilename]);
        $this->deleteImage($request);

        $request->merge(
            [
                'filename' => $oldFilename,
                'is_update' => true,
                'document_id' => $documentId,
            ]
        );
        $updateImage = $this->uploadImage($request)->getData();

        return $updateImage;
    }


    public function getImagePath(Request $request)
    {
        try {
            $filename = $request->input('filename');

            // cek file exist atau tidak
            $isExist = $this->isExist($request)->getData();
            if ($isExist->status === 'error') {
                return response()->json(['status' => 'success', 'message' => 'file tidak exist'], 200);
            }

            // mendapatkan url gambar
            $storage = app('firebase.storage');
            $url = $storage->getBucket()->object($filename)->signedUrl(new \DateTime('+1 hour'));

            return response()->json(['status' => 'success', 'message' => 'berhasil upload gambar', 'data' => $url], 200);
        } catch (FirebaseException $e) {
            return response()->json(['status' => 'error', 'message' => 'gagal mendapatkan URL gambar: ' . $e->getMessage()], 500);
        }
    }

    public function isExist(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'filename' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $fileName = $request->input('filename');


        $storage = app('firebase.storage');

        // Dapatkan bucket Firebase Storage
        $bucket = $storage->getBucket();

        // Cek apakah file ada dalam bucket
        $object = $bucket->object($fileName);
        $exists = $object->exists();

        // Respon berdasarkan keberadaan file
        if ($exists) {
            return response()->json(['status' => 'success', 'message' => "File $fileName ditemukan di Firebase Storage."], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => "File $fileName tidak ditemukan di Firebase Storage."], 404);
        }
    }
}
