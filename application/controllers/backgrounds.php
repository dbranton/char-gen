<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backgrounds extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model('background_model');
        $data['backgrounds'] = $this->background_model->getBackgrounds();
        $data['title'] = 'Backgrounds';
        $data['main_content'] = 'pages/backgrounds_view';
        $this->load->view('template', $data);
    }
}
