<?php

namespace App\Controllers;

class Home extends BaseController
{
    private $db = null;

    function __construct(){
        date_default_timezone_set("Asia/Bangkok");
        $this->db = db_connect();
        $session = service('session');
    }

    public function testEsp(){
        $data = [
            'isi' => "isi",
          

        ];

        // Insert data into the table
        $builder = $this->db->table('esp');
        $builder->upsert($data);
        // if ($builder->upsert($data)) {

        // }
    }

    public function index(): string
    {
        return view('welcome_message');
        //return view('pembayaran');
        // return view('admin');
    }

    public function checked_in(){
        return view('checked_in');
        // $this->session_setter();
    }

    public function session_setter(){
        $_SESSION['check_in'] = "true";
        return redirect()->to(base_url("checkin_list"));
    }

    public function session_unsetter(){
         unset($_SESSION['check_in']);
         return redirect()->to(base_url("checkin_list"));
    }

    public function checkin_list(){
        $_SESSION['check_in'] = "true";
        $builder = $this->db->table('pendaftar')->where("flag_checkin IS NOT NULL",null, false);

        $query   = $builder->get();
     
        return view('checkin_list',['admin' => $query->getResult()]);

    }

    public function tiket(){

        $builder = $this->db
        ->table('pendaftar')
        ->where('ticket_no',$_GET['no']);

        $query   = $builder->get();
        // echo count($query->getResult());
        if (count($query->getResult()) > 0 ) {
            $no_tiket = $_GET['no'];


            if (isset($_SESSION['check_in'])) {
                $now = new \DateTime();

                $data = [
                    'flag_checkin' => $now->format('Y-m-d H:i:s'),
                ];

                $builder->where('ticket_no', $no_tiket);
                if (!$builder->update($data)) {
                    return view('tiket_notsent');
                } else {
                    return view('checked_in', ['result' => $query->getResult()]);
                }
            }else{
                return view('tiket',['tiket' => $no_tiket, 'result' => $query->getResult()]);
            }
            
        }else{
            return view('ticket_not_found');
        }
        // echo json_encode($query->getResult());

        
    }

    public function daftar(){
        $nama = $this->request->getPost('nama', FILTER_SANITIZE_STRING);
        $email = $this->request->getPost('email', FILTER_SANITIZE_EMAIL);
        $grade = $this->request->getPost('grade', FILTER_SANITIZE_EMAIL);
        $hp = $this->request->getPost('hp', FILTER_SANITIZE_STRING);
        $occupation = $this->request->getPost('occupation', FILTER_SANITIZE_STRING);

         $timestamp = time() % 100000; // Current timestamp in milliseconds
        $randomPart = rand(0, 99);  // 4-digit random number
        $no_tiket = "T-{$timestamp}{$randomPart}";

        // Prepare data array
        $data = [
            'email' => $email,
            'grade' => $grade,
            'nama' => $nama,
            'hp' => $hp,
            'occupation' => $occupation,
            'ticket_no' => $no_tiket

        ];

        // Insert data into the table
        $builder = $this->db->table('pendaftar');
        if ($builder->upsert($data)) {
            // Set success message in session
            $this->session->setFlashdata('result', 'sukses');
            
            // Attempt to send confirmation email
            if ($this->send_ticket($nama, $email,$no_tiket)) {
                return redirect()->to(base_url("daftar_sukses?nama=$nama&email=$email&no_tiket=$no_tiket"));
            } else {
                // Handle case where email could not be sent
                $this->session->setFlashdata('result', 'Email gagal dikirim. Silakan coba lagi.');
                //return redirect()->to(base_url("daftar_gagal"));
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
        $email_smtp->setSubject("Registration Confirmation: International Montessori Seminar with Dr. Paul Epstein");
        $email_smtp->setMessage("Thank you!
We have received your registration $nama  
Below is the information regarding the registration Fee for the International Montessori Seminar with Dr. Paul Epstein:

Early Bird Price (November 9–12, 2024): IDR 125,000  
Normal Price (November 13–15, 2024): IDR 200,000  

Please make the payment via BANK TRANSFER to:  

Bank: CIMB NIAGA  
Account Number: 800191717900  
Account Name: Pendidikan dan Sosial Kasih Bunda Malang  
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

    public function resend_ticket(){
       
         if (!$this->send_ticket($_GET['nama'],$_GET['email'],$_GET['no_tiket'])) {
                 return view('tiket_notsent');
            }else{
                 return view('tiket_sent');
            }
    }



    public function send_ticket($namanya, $emailnya,$no_tiket){
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

       
       // return "T{$timestamp}{$randomPart}";

        $nama = $namanya;
        $email = $emailnya;

        $email_smtp->setFrom("mli_event@sinarumi.co.id");
        $email_smtp->setTo("$email");
        $email_smtp->setSubject("E-Ticket: Chinese New Year Celebration");
        $email_smtp->setMessage("
Dear $nama,

Thank you, we have received your registration.
  
Below is your entrance ticket link to attend the Chinese New Year Celebration

Please show your ticket during the re-registration process.  

https://sinarumi.co.id/paulseminarregistration/public/tiket?no=$no_tiket

*Note*:  
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
