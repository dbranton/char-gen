<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feats extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model('feat_model');
        $data['feats'] = $this->feat_model->getFeats();
        $data['title'] = 'Feats';
        $data['main_content'] = 'pages/feats_view';
        $data['type'] = '';
        $this->load->view('template', $data);
    }
}
