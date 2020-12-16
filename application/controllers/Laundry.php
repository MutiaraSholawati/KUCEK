<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Laundry extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('laundry_model', 'dry');
        $this->methods['index_get']['limit'] = 2;
        
    }

public function index_get(){
    $id = $this->get('id', true);
    if ($id === null) {
        $p = $this->get('page', true);
        $p = (empty($p) ? 1 : $p);
        $total_data = $this->dry->count();
        $total_page = ceil($total_data / 5);
        $start = ($p - 1) * 5;
        $list = $this->dry->get(null, 5, $start);
        if ($list) {
            $data = [
            'status' => true,
            'halaman' => $p,
            'total_data' => $total_data,
            'total_halaman' => $total_page,
            'data' => $list
            ];
        } else {
            $data = [
            'status' => false,
            'msg' => 'Data tidak ditemukan'
            ];
        }
        $this->response($data, RestController::HTTP_OK);
    } 
    else {
        $data = $this->dry->get($id);
        if ($data) {
            $this->response(['status' => true, 'data' => $data], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'msg' => $id . ' tidak ditemukan'], RestController::HTTP_NOT_FOUND);
        }
    }
}

public function index_post()
  {
    $data = [
      'kd_laundry' => $this->post('kode', true),
      'nama_laundry' => $this->post('nama', true),
      'alamat' => $this->post('alamat', true),
      'kontak' => $this->post('kontak', true)
    ];
    $simpan = $this->dry->add($data);
    if ($simpan['status']) {
      $this->response(['status' => true, 'msg' => $simpan['data'] . ' Data telah ditambahkan'], RestController::HTTP_CREATED);
    } else {
      $this->response(['status' => false, 'msg' => $simpan['msg']], RestController::HTTP_INTERNAL_ERROR);
    }
  }

  public function index_put()
  {
    $data = [
        'kd_laundry' => $this->put('kode', true),
        'nama_laundry' => $this->put('nama', true),
        'alamat' => $this->put('alamat', true),
        'kontak' => $this->put('kontak', true)
    ];
    $id = $this->put('kode', true);
    if ($id === null) {
      $this->response(['status' => false, 'msg' => 'Masukkan kode laundry yang akan dirubah'], RestController::HTTP_BAD_REQUEST);
    }
    $ubah = $this->dry->update($id, $data);
    if ($ubah['status']) {
      $status = (int)$ubah['data'];
      if ($status > 0)
        $this->response(['status' => true, 'msg' => $ubah['data'] . ' Data telah dirubah'], RestController::HTTP_OK);
      else
        $this->response(['status' => false, 'msg' => 'Tidak ada data yang dirubah'], RestController::HTTP_BAD_REQUEST);
    } else {
      $this->response(['status' => false, 'msg' => $ubah['msg']], RestController::HTTP_INTERNAL_ERROR);
    }
  }

  public function index_delete()
  {
    $id = $this->delete('kode', true);
    if ($id === null) {
      $this->response(['status' => false, 'msg' => 'Masukkan kode laundry yang akan dihapus'], RestController::HTTP_BAD_REQUEST);
    }
    $delete = $this->dry->delete($id);
    if ($delete['status']) {
      $status = (int)$delete['data'];
      if ($status > 0)
        $this->response(['status' => true, 'msg' => 'data dengan kode '. $id . ' telah dihapus'], RestController::HTTP_OK);
      else
        $this->response(['status' => false, 'msg' => 'Tidak ada data yang dihapus'], RestController::HTTP_BAD_REQUEST);
    } else {
      $this->response(['status' => false, 'msg' => $delete['msg']], RestController::HTTP_INTERNAL_ERROR);
    }
  }


}