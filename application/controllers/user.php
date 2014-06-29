<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
    }

    public function login() {

        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');

        /*$username = $this->input->post('username');
        $password = md5($this->input->post('password'));
        $result = $this->user_model->login($username,$password);*/
        //if ($result) {
        if ($this->form_validation->run() == TRUE) {
            $data['title'] = 'Home';
            $data['main_content'] = 'pages/home';
        } else {
            $data['title'] = 'Login';
            $data['main_content'] = 'pages/login_view';
        }
        $this->load->view('template', $data);
    }

    public function register() {
        // field name, error message, validation rules
        $this->form_validation->set_rules('username', 'User Name', 'trim|required|min_length[4]|xss_clean');
        $this->form_validation->set_rules('email', 'Your Email', 'trim|valid_email');   // not required
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');
        $this->form_validation->set_message('valid_email', 'Your email is not valid');


        if ($this->form_validation->run() == TRUE) {
            $this->user_model->add_user();
            $data['title'] = 'Home';
            $data['main_content'] = 'pages/home';
        } else {
            $data['title'] = 'Register';
            $data['main_content'] = 'pages/register_view';
        }
        $this->load->view('template', $data);
    }

    public function check_database($password) {
        // Field validation succeeded. Validate against database
        $username = $this->input->post('username');

        // query the database
        $result = $this->user_model->login($username, $password);
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

    public function logout() {
        $this->session->sess_destroy();
        $data['title'] = 'Home';
        $data['main_content'] = 'pages/home';
        $this->load->view('template', $data);
    }

    public function saveCharacter() {
        $this->form_validation->set_rules('charName', 'Name', 'required');
        $this->form_validation->set_rules('raceName', 'Race', 'required');
        $this->form_validation->set_rules('backgroundName', 'Background', 'required');
        $this->form_validation->set_rules('className', 'Class', 'required');
        $this->form_validation->set_rules('subclassName', 'Subclass', 'required');

        if ($this->form_validation->run() == TRUE) {
            $this->user_model->add_character();
            //$data['characters'] = $this->user_model->get_characters();
            $data['title'] = 'Character Submitted';
            $data['main_content'] = 'pages/success';
        } else {
            //$data['title'] = 'Register';
            //$data['main_content'] = 'pages/register_view';
        }
        $this->load->view('template', $data);
    }

    public function yourCharacters() {
        $data['characters'] = $this->user_model->get_characters();
        $data['title'] = 'Your Characters';
        $data['main_content'] = 'pages/characters_view';
        $this->load->view('template', $data);
    }

    public function character($characterId) {
        if (!empty($this->session->userdata['logged_in'])) {
            $data['character'] = $this->user_model->get_character($characterId);
            $data['title'] = $data['character']['name'];
            $data['main_content'] = 'pages/character_view';
            $this->load->view('template', $data);
        } else {
            $this->login();
        }
    }
}

?>