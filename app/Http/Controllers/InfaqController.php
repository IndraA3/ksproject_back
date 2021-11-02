<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InfaqController extends Controller
{
    public function index()
    {
      $user = Auth::guard('sanctum')->user();
      if ($user->tokenCan('app:zakat')){
        $infaq = DB::table('infaq')
                      ->where('deleted_at', null)
                      ->paginate(10);
  
        return response($infaq, 200);
      }
      return response('Forbidden', 403);
    }

    public function store(Request $req)
    {
      $user = Auth::guard('sanctum')->user();
      if ($user->tokenCan('app:zakat')){
        $req->validate([
          'nama' => 'required|max:100',
          'jumlah' => 'required|numeric',
        ]);
  
        try {
          DB::table('infaq')->insert([
            'nama' => $req['nama'],
            'jumlah' => $req['jumlah'],
            'created_at' => now(),
            'updated_at' => now(),
          ]);
          return response('Data berhasil disimpan', 201);
  
        } catch (QueryException $ex) {
          return response($ex, 400);
        }
      }
      return response('Forbidden', 403);

    }

    public function search($keyword)
    {
      $user = Auth::guard('sanctum')->user();
      if ($user->tokenCan('app:zakat')){
        $infaq = DB::table('infaq')
                      ->where([
                                'deleted_at' => null,
                              ])
                        ->where('nama', 'like', '%'.$keyword.'%')
                        ->paginate(10);
  
        return response($infaq, 200);
      }
      return response('Forbidden', 403);
    }

    public function update(Request $req, $id)
    {
      $user = Auth::guard('sanctum')->user();
      if ($user->tokenCan('app:zakat')){
        $req->validate([
          'nama' => 'required|max:100',
          'jumlah' => 'required|numeric',
        ]);
  
        try {
  
          DB::table('infaq')->where('id', $id)->update([
            'nama' => $req['nama'],
            'jumlah' => $req['jumlah'],
            'updated_at' => now(),
          ]);
  
          return response('Data Disimpan', 201);
  
        } catch (QueryException $ex) {
          return response($ex, 400);
        }
      }
      return response('Forbidden', 403);
    }

    public function softDelete($id)
    {
      $user = Auth::guard('sanctum')->user();
      if ($user->tokenCan('app:zakat')){
        try {
  
          DB::table('infaq')->where('id', $id)->update([
            'deleted_at' => now()
          ]);
          return response('Data berhasil terhapus', 200);
  
        } catch (QueryException $ex) {
          return response('Ups, Something went wrong '.$ex, 400);
        }
      }
      return response('Forbidden', 403);
      
    }

    public function deleted()
    {
      $user = Auth::guard('sanctum')->user();
      if ($user->tokenCan('app:zakat') && $user->tokenCan('zakat:admin')){
        $deletedInfaq = DB::table('infaq')->where('deleted_at', '!=', null)->paginate(10);
  
        return response($deletedInfaq, 200);
      }
      return response('Forbidden', 403);
    }

    public function searchDeleted($keyword)
    {
      $user = Auth::guard('sanctum')->user();
      if ($user->tokenCan('app:zakat') && $user->tokenCan('zakat:admin')){
        $infaq = DB::table('infaq')
                      ->where('deleted_at', '!=', null)
                      ->where('nama', 'like', '%'.$keyword.'%')
                      ->paginate(10);
  
        return response($infaq, 200);
      }
      return response('Forbidden', 403);
    }

    public function restore($id)  
    {
      $user = Auth::guard('sanctum')->user();
      if ($user->tokenCan('app:zakat') && $user->tokenCan('zakat:admin')){
        try {
          DB::table('infaq')->where('id', '=', $id)->update([
            'deleted_at' => null
          ]);
  
          return response('success', 200);
  
        } catch (QueryException $ex) {
          return response('Ups Something wen wrong error:'.$ex, 400);
        }
      }
      return response('Forbidden', 403);
    }
}
