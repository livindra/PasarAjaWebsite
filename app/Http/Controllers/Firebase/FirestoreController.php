<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FirestoreController extends Controller
{

    public function isExistCollection(Request $request)
    {
        try {
            $collectionName = $request->input('collection');

            // inisialisasi firebase
            $firestore = app('firebase.firestore');

            // cek apakah collection ada atau tidak
            $collectionRef = $firestore->database()->collection($collectionName);
            $exists = $collectionRef->documents()->isEmpty();

            if (!$exists) {
                return response()->json(['status' => 'success', 'message' => 'Collection ditemukan'], 200);
            } else {

                return response()->json(['status' => 'error', 'message' => 'Collection tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function createCollection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection' => 'required'
        ], [
            'collection.required' => 'Nama collection harus diisi'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 400);
        }

        $name = $request->input('collection');

        try {
            // cek apakah collection sudah terpakai atau belum
            $isExist = $this->isExistCollection($request)->getData();
            if ($isExist->status === 'success') {
                return response()->json(['status' => 'error', 'message' => 'Nama collection sudah dipakai'], 400);
            }

            // buat collection baru
            $firestore = app('firebase.firestore');
            $collectionRef = $firestore->database()->collection($name);

            // buat empty data di collection yang baru
            $data = [
                'fullname' => 'Syamdani Ardianto',
                'gender' => 'rather not say',
            ];
            $collectionRef->add($data);

            return response()->json(['status' => 'success', 'message' => 'Collection berhasil dibuat'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal membuat collection: ' . $e->getMessage()], 500);
        }
    }

    public function deleteCollection(Request $request)
    {
        try {

            $collectionName = $request->input('collection');

            // inisialisasi collection
            $firestore = app('firebase.firestore');
            $collectionRef = $firestore->database()->collection($collectionName);

            // menghapus semua data dalam collection
            $documents = $collectionRef->documents();
            foreach ($documents as $document) {
                $document->reference()->delete();
            }

            return response()->json(['status' => 'success', 'message' => 'Collection berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' =>  $e->getMessage()], 500);
        }
    }
    public function getAllStudent()
    {
        try {

            // inisialisasi collection
            $firestore = app('firebase.firestore');
            $collectionRef = $firestore->database()->collection("0students");

            // mendapatkan semua data dalam collection
            $documents = $collectionRef->documents();

            $data = [];

            // membaca semua data dalam dokumen
            foreach ($documents as $document) {

                // mendapatkan data dari dokumen
                $docData = $document->data();

                // mendapatkan id dokumen
                $docId = $document->id();
                $docData['id'] = $docId;

                // save data
                $data[] = $docData;
            }

            return response()->json(['status' => 'success', 'message' => 'Data berhasil didapatkan', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mendapatkan data: ' . $e->getMessage()], 500);
        }
    }

    public function isExistNim(Request $request)
    {
        try {

            $nim = $request->input('nim');

            // inisialisasi collection
            $firestore = app('firebase.firestore');
            $collectionRef = $firestore->database()->collection("0students");

            // mendapatkan data nim
            $query = $collectionRef
                ->where('nim', '=', strtoupper($nim))
                ->limit(1)
                ->documents();

            // return response
            if (!$query->isEmpty()) {
                return response()->json(['status' => 'success', 'message' => 'NIM exist'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'NIM not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mendapatkan data: ' . $e->getMessage()], 500);
        }
    }

    public function getDocumentIdByNim(Request $request)
    {
        try {
            $nim = $request->input('nim');

            // Inisialisasi collection
            $firestore = app('firebase.firestore');
            $collectionRef = $firestore->database()->collection("0students");

            // Mendapatkan dokumen dengan NIM tertentu
            $query = $collectionRef
                ->where('nim', '=', strtoupper($nim))
                ->limit(1)
                ->documents();

            // mendapatkan id dari document
            if (!$query->isEmpty()) {
                foreach ($query as $document) {
                    $docId = $document->id();
                    return response()->json(['status' => 'success', 'message' => 'Document found', 'document_id' => $docId], 200);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'NIM tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['status' => 'error', 'message' => 'Gagal mendapatkan data: ' . $e->getMessage()], 500);
        }
    }

    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required',
            'name' => 'required',
            'age' => 'required|integer',
            'gender' => 'required|in:Male,Female',
        ], [
            'nim.required' => 'NIM harus diisi',
            'name.required' => 'Nama harus diisi',
            'age.required' => 'umur harus disi',
            'age.integer' => 'umur harus dalam angka',
            'gender.required' => 'Gender harus male / female',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $nim = $request->input('nim');
        $name = $request->input('name');
        $age = $request->input('age');
        $gender = $request->input('gender');

        try {
            // cek apakah nim sudah terdaftar atau belum
            $isExistNim = $this->isExistNim($request)->getData();
            if ($isExistNim->status === 'success') {
                return response()->json(['status' => 'error', 'message' => 'NIM sudah terpakai'], 400);
            }

            // menambahkan data ke collection
            $firestore = app('firebase.firestore')
                ->database()
                ->collection("0students")
                ->newDocument();
            $firestore->set([
                "nim" => strtoupper($nim),
                "name" => $name,
                "age" => intval($age),
                "gender" => $gender,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Data berhasil ditambahkan'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menambahkan data: ' . $e->getMessage()], 500);
        }
    }

    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'age' => 'required|integer',
            'gender' => 'required|in:Male,Female',
        ], [
            'id.required' => 'ID harus diisi',
            'name.required' => 'Nama harus diisi',
            'age.required' => 'Umur harus diisi',
            'age.integer' => 'Umur harus dalam angka',
            'gender.required' => 'Gender harus male / female',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $id = $request->input('id');
        $name = $request->input('name');
        $age = $request->input('age');
        $gender = $request->input('gender');

        try {
            // Update data pengguna
            $firestore = app('firebase.firestore');
            $documentRef = $firestore->database()->collection("0students")->document($id);

            // Update the document
            $documentRef->set([
                "name" => $name,
                "age" => intval($age),
                "gender" => $gender,
                "updated_at" => Carbon::now(),
            ], ['merge' => true]); // Merges the new data with existing data

            return response()->json(['status' => 'success', 'message' => 'Data berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }


    public function deleteUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ], [
            'id.required' => 'ID harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }
        $id = $request->input('id');

        try {
            app('firebase.firestore')
                ->database()
                ->collection('0students')
                ->document($id)
                ->delete();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus'], 200);
        } catch (\Exception $ex) {
            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
        }
    }
}
