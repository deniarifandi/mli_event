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
        // $ttl = $_POST['ttl'];

      
        $builder = $this->db->table('pendaftar');
         $data = [
            'nama' => $nama,
            'email' => $email,
            'hp' => $hp,
            // 'ttl' => $ttl
        ];
        
        if ($builder->insert($data) === TRUE) {
            echo "berhasil brother";
            $this->session->set('result', 'sukses');
            $this->session->markAsFlashdata('result');
            header('Location: '.base_url().'daftar_sukses'); 
            die();
        }else{
            echo "gagal brother";
            $this->session->set('result', 'gagal');
            $this->session->markAsFlashdata('result');
           //header('Location: '.base_url().'va/va_admin'); 
            die();
        }

    }

    public function daftar_sukses(){
        return view('daftar_sukses');
    }

    public function admin(){

        $builder = $this->db->table('pendaftar');

        $query   = $builder->get();

        // echo json_encode($query->getResult());
        // return $query->getResult(); 
        return view('admin',['admin' => $query->getResult()]);
    }

    public function send_email(){
        $email_smtp = \Config\Services::email();

        $config["protocol"] = "mail";
        $config["SMTPHost"]  = "mail.sinarumi.co.id";
        $config["SMTPUser"]  = "mli_event@sinarumi.co.id";
        $config["SMTPPass"]  = "n@PnMwkB#k3@";
        $config["SMTPPort"]  = 465;
        $config["SMTPCrypto"] = "ssl";

        $email_smtp->initialize($config);

        $email_smtp->setFrom("mli_event@sinarumi.co.id");
        $email_smtp->setTo("arifandi.malang@gmail.com");
        $email_smtp->setSubject("Ini subjectnya");
        $email_smtp->setMessage("Ini isi/body email");

        if (!$email_smtp->send()) {
            // Print error details if email sending fails
            echo "Failed to send email. Error details:<br>";
            echo $email_smtp->printDebugger(['headers']);
        } else {
            echo "Email sent successfully!";
        }
    }
}
