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
        $occupation = $this->request->getPost('occupation', FILTER_SANITIZE_STRING);

        // Prepare data array
        $data = [
            'nama' => $nama,
            'email' => $email,
            'hp' => $hp,
            'occupation' => $occupation

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
        $email_smtp->setSubject("Registration Confirmation: Montessori Seminar with Dr. Paul Epstein");
        $email_smtp->setMessage("Thank you!
We have received your registration $nama  
Below is the information regarding the Registration Fee for the Montessori Seminar with Paul Epstein:  

Early Bird Price (September 9–12, 2024): IDR 100,000  
Normal Price (September 13–15, 2024): IDR 175,000  

Please make the payment via BANK TRANSFER to:  

Bank: BCA  
Account Number: 1234567890  
Account Name: MASIH BELUM TAHU  
Payment Reference: SEMINAR a.n *PENDAFTAR

Confirm your payment by sending the proof of payment to the administrative WhatsApp at the number: 082-332-686-310. 
 
Once we confirm your payment, the entrance ticket will be sent to your active email within a maximum of 24 hours.

If you have any issues or questions regarding the registration process, you can contact the administrative WhatsApp at 082-332-686-310

Thank you!
Have a nice day!");

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

        $timestamp = time() % 100000; // Current timestamp in milliseconds
        $randomPart = rand(0, 99);  // 4-digit random number
        $no_tiket = "T{$timestamp}{$randomPart}";
       // return "T{$timestamp}{$randomPart}";

        $nama = $_GET['nama'];
        $email = $_GET['email'];

        $email_smtp->setFrom("mli_event@sinarumi.co.id");
        $email_smtp->setTo("$email");
        $email_smtp->setSubject("E-Ticket : Montessori Seminar with Dr. Paul Epstein");
        $email_smtp->setMessage("
Dear $nama,

Thank you, we have received your payment.
  
Attached is your entrance ticket to attend the seminar \"Raising Resilient Children: Montessori Approaches for COVID-ERA CHALLENGES\" with Dr. Paul Epstein.

https://sinarumi.co.id/paulseminarregistration/public/tiket?no=$no_tiket

Please show your ticket during the re-registration process.  

Note: 
Kindly arrive 30 minutes before the event starts, as there will be re-registration.

Thank you! See you soon.
            ");

        if (!$email_smtp->send()) {
            // Print error details if email sending fails
            echo "Failed to send email. Error details:<br>";
            echo $email_smtp->printDebugger(['headers']);
        } else {
            // echo "Email sent successfully!";

            $data = [
                'flag_tiket' => 1,
                'ticket_no' => $no_tiket
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
