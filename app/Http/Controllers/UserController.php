<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //menampilkan semua kelas di halaman beranda
    public function showAllClass(){
        $class_open = Kelas::where('id_status','=', 4)->get();
        $class_close = Kelas::where('id_status','=', 6)->get();
            if(sizeof($class_open) > 0){
                return response()->json([
                    'status' => 'Success',
                    'data' => [
                        'kelas yang buka' => $class_open->toArray(),
                        'kelas yang tutup' => $class_close->toArray(),
                    ],
                ],200);
            }else{
                return response()->json([
                    'status' => 'Success',
                    'data' => [
                        'kelas yang tutup' => $class_close->toArray()
                    ],
                ],200);
            }
    }
}
