<?php

namespace App\Controllers;

class Home extends BaseController
{
    private $db = null;

    function __construct(){

        $this->db = db_connect();
        $session = service('session');
    }

    public function index(): string
    {
        return view('welcome_message');
        //return view('pembayaran');
        // return view('admin');
    }

    public function daftar(){
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $hp = $_POST['hp'];
        $ttl = $_POST['ttl'];

      
        $builder = $this->db->table('pendaftar');
         $data = [
            'nama' => $nama,
            'email' => $email,
            'hp' => $hp,
            'ttl' => $ttl
        ];
        
        if ($builder->insert($data) === TRUE) {
            echo "berhasil brother";
            $this->session->set('result', 'sukses');
            $this->session->markAsFlashdata('result');
            //header('Location: '.base_url().'va/va_admin'); 
            die();
        }else{
            echo "berhaixiudsil brother";
            $this->session->set('result', 'gagal');
            $this->session->markAsFlashdata('result');
           //header('Location: '.base_url().'va/va_admin'); 
            die();
        }

    }
}
