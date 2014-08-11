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
        $data['type'] = 'user';
        $this->load->view('template', $data);
    }

    public function register() {
        // field name, error message, validation rules
        $this->form_validation->set_rules('username', 'User Name', 'trim|required|min_length[4]|xss_clean');
        $this->form_validation->set_rules('email', 'Your Email', 'trim|valid_email');   // not required
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');
        $this->form_validation->set_message('valid_email', 'Your email is not valid');
        $data['type'] = '';

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
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $character = $request->character;
        if (isset($this->session->userdata['logged_in'])) {
            $user_id = $this->session->userdata['logged_in']['id'];
            //$data['character'] = $this->user_model->add_character($user_id, $character);    // for testing only
            $this->user_model->add_character($user_id, $character);
        } else {
            // throw error/comment out when local storage is implemented
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'User is not logged in')));
        }

        // for testing only
        /*$data['title'] = 'Character Submitted';
        $data['main_content'] = 'pages/success';
        $this->load->view('template', $data);*/
    }

    public function deleteCharacter($charId) {
        $this->user_model->delete_character($charId);
    }

    public function yourCharacters() {
        if (isset($this->session->userdata['logged_in'])) {
            $data['title'] = 'Your Characters';
            $data['main_content'] = 'pages/characters_view';
            $data['type'] = 'user';
            $this->load->view('template', $data);
        } else {
            $this->login();
        }
    }

    public function getCharacters() {
        $data = $this->user_model->get_characters();
        print json_encode($data);
    }

    public function character_generator() {
        $this->load->library('user_agent');
        $data['mobile'] = $this->agent->is_mobile();
        $data['title'] = 'Character Generator';
        $data['type'] = 'character_generator';
        $data['main_content'] = 'pages/character_generator_view';  // races view works the same for classes
        $this->load->view('template', $data);
    }

    public function character($characterId) {
        if (!empty($this->session->userdata['logged_in'])) {
            $data['character'] = $this->user_model->get_character($characterId);
            $data['title'] = $data['character']['name'];
            $data['main_content'] = 'pages/character_view';
            $data['type'] = '';
            $this->load->view('template', $data);
        } else {
            $this->login();
        }
    }

    public function checkIfLoggedIn() {
        print json_encode(isset($this->session->userdata['logged_in']));
    }
}

?>