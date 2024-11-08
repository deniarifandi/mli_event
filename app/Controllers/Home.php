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

    public function tiket(){
        $no_tiket = $_GET['no'];
        return view('tiket',['tiket' => $no_tiket]);
    }

    public function daftar(){
        $nama = $this->request->getPost('nama', FILTER_SANITIZE_STRING);
        $email = $this->request->getPost('email', FILTER_SANITIZE_EMAIL);
        $hp = $this->request->getPost('hp', FILTER_SANITIZE_STRING);

        // Prepare data array
        $data = [
            'nama' => $nama,
            'email' => $email,
            'hp' => $hp,
        ];

        // Insert data into the table
        $builder = $this->db->table('pendaftar');
        if ($builder->insert($data)) {
            // Set success message in session
            $this->session->setFlashdata('result', 'sukses');
            
            // Attempt to send confirmation email
            if ($this->send_konfirmasi_pendaftaran($nama, $email)) {
                return redirect()->to(base_url("daftar_sukses?nama=$nama&email=$email"));
            } else {
                // Handle case where email could not be sent
                $this->session->setFlashdata('result', 'Email gagal dikirim. Silakan coba lagi.');
                return redirect()->to(base_url("daftar_gagal"));
            }
        } else {
            // Set failure message in session
            $this->session->setFlashdata('result', 'gagal');
            return redirect()->to(base_url("daftar_gagal"));
        }

    }

    public function daftar_sukses(){
        $nama = $_GET['nama'];
        $email = $_GET['email'];
         return view('daftar_sukses',['nama'=> $nama, 'email'=>$email]);
    }

     public function daftar_gagal(){
        
         return view('daftar_gagal');
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
        $email_smtp->setMessage("\n\nTerima kasih $nama Telah melakukan Pendaftaran.\n 
Selanjutnya, silakan melakukan pembayaran sejumlah 100.000 ke nomor rekening dibawah ini. \n\n\n
xxx-xxxx-xxxx \n
A/n xxxx xxxxxxxx\n\n
Dan kirimkan bukti pembayaran ke Nomor Whatsapp \n\n
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

    public function admin(){

        $builder = $this->db->table('pendaftar');

        $query   = $builder->get();

        // echo json_encode($query->getResult());
        // return $query->getResult(); 
        return view('admin',['admin' => $query->getResult()]);
    }



    public function send_ticket(){
        $email_smtp = \Config\Services::email();
        $builder = $this->db->table('pendaftar');
        $config["protocol"] = "smtp";
        $config["SMTPHost"]  = "mail.sinarumi.co.id";
        $config["SMTPUser"]  = "mli_event@sinarumi.co.id";
        $config["SMTPPass"]  = "n@PnMwkB#k3@";
        $config["SMTPPort"]  = 465;
        $config["SMTPCrypto"] = "ssl";
      
        $config['smtp_port'] = 587;

        $email_smtp->initialize($config);

        $nama = $_GET['nama'];
        $email = $_GET['email'];

        $email_smtp->setFrom("mli_event@sinarumi.co.id");
        $email_smtp->setTo("$email");
        $email_smtp->setSubject("Ticket Event XXXXXX");
        $email_smtp->setMessage("
Dear $nama,

Berikut terlampir link Ticket untuk Event XXXXX

https://sinarumi.co.id/ticket

Terima Kasih,

");

        if (!$email_smtp->send()) {
            // Print error details if email sending fails
            echo "Failed to send email. Error details:<br>";
            echo $email_smtp->printDebugger(['headers']);
        } else {
            // echo "Email sent successfully!";

            $data = [
                'flag_tiket' => 1,
            ];

            $builder->where('email', $email);
            if (!$builder->update($data)) {
                 return view('tiket_notsent');
            }else{
                 return view('tiket_sent');
            }
            
        }
    }

    public function tiket_sent(){
        return view('tiket_sent');
    }

    public function tiket_notsent(){
        return view('tiket_notsent');
    }
}
