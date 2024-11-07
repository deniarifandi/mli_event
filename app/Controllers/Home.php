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
       
        $builder = $this->db->table('pendaftar');
         $data = [
            'nama' => $nama,
            'email' => $email,
            'hp' => $hp,
            // 'ttl' => $ttl
        ];
        
        if ($builder->insert($data) === TRUE) {
            // echo "berhasil brother";
            $this->session->set('result', 'sukses');
            $this->session->markAsFlashdata('result');
            if($this->send_konfirmasi_pendaftaran($nama, $email) == "sukses")
             return view('daftar_sukses',['nama'=> $nama, 'email'=>$email]);
            die();
        }else{
            // echo "gagal brother";
            $this->session->set('result', 'gagal');
            $this->session->markAsFlashdata('result');
           //header('Location: '.base_url().'va/va_admin'); 
            die();
        }

    }
    public function send_konfirmasi_pendaftaran($nama, $email){
        $email_smtp = \Config\Services::email();

        $config["protocol"] = "smtp";
        $config["SMTPHost"]  = "mail.sinarumi.co.id";
        $config["SMTPUser"]  = "mli_event@sinarumi.co.id";
        $config["SMTPPass"]  = "n@PnMwkB#k3@";
        $config["SMTPPort"]  = 465;
        $config["SMTPCrypto"] = "ssl";
      
        $config['smtp_port'] = 587;

        $email_smtp->initialize($config);

        $email_smtp->setFrom("mli_event@sinarumi.co.id");
        $email_smtp->setTo("$email");
        $email_smtp->setSubject("Konfirmasi Pendaftaran Event XXXXXX");
        $email_smtp->setMessage("Terima kasih $nama Telah melakukan Pendaftaran.\n 
Selanjutnya, silakan melakukan pembayaran sejumlah 100.000 ke nomor rekening dibawah ini. \n\n\n
xxx-xxxx-xxxx \n
A/n xxxx xxxxxxxx\n\n
Dan kirimkan bukti pembayaran ke Nomor Whatsapp \n
xxx-xxxx-xxxx\n\n
Pembayaran akan divalidasi, dan tiket akan dikirimkan dalam waktu 1x24 jam.
");

        if (!$email_smtp->send()) {
            // Print error details if email sending fails
            echo "Failed to send email. Error details:<br>";
            echo $email_smtp->printDebugger(['headers']);
        } else {
            return "sukses";
            
        }
    }

    public function daftar_sukses($nama, $email){
        return view('daftar_sukses',['nama'=> $nama, 'email'=>$email]);
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

        $config["protocol"] = "smtp";
        $config["SMTPHost"]  = "mail.sinarumi.co.id";
        $config["SMTPUser"]  = "mli_event@sinarumi.co.id";
        $config["SMTPPass"]  = "n@PnMwkB#k3@";
        $config["SMTPPort"]  = 465;
        $config["SMTPCrypto"] = "ssl";
      
        $config['smtp_port'] = 587;

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
