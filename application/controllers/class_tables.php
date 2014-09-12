<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_Tables extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model('class_model');
        $data['classes'] = $this->class_model->getClasses();
        $data['title'] = 'Classes';
        $data['main_content'] = 'pages/classes';  // races view works the same for classes
        $data['type'] = '';
        $this->load->view('template', $data);
    }

    public function wild_magic_table() {
        $data['title'] = 'Wild Magic Surge Table';
        $data['main_content'] = 'pages/wild_magic_table';
        $data['type'] = '';
        $this->load->view('template', $data);
    }
}
?>
