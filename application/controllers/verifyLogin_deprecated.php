<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VerifyLogin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('user_model');
    }

    function index() {
        // This method will have the credentials validation
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
        //$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            // Field validation failed. User redirected to login page
            $data['title'] = 'Login';
            $data['main_content'] = 'pages/login_view';  // races view works the same for classes
            $this->load->view('template', $data);
        } else {
            // Go to private area
            redirect('home', 'refresh');
        }
    }

    function check_database($password) {
        // Field validation succeeded. Validate against database
        $username = $this->input->post('username');

        // query the database
        $result = $this->user->login($username, $password);
        if ($result) {
            foreach ($result as $row) {
                $sess_array = array(
                    'id' => $row->id,
                    'username' => $row->username
                );
                $this->session->set_userdata('logged_in', $sess_array);
            }
            return TRUE;
        } else {
            $this->form_validation->set_message('check_database', 'Invalid username or password');
            return FALSE;
        }
    }

}

?>