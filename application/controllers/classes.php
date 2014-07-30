<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Classes extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model('class_model');
        $data['classes'] = $this->class_model->getClasses();
        $data['title'] = 'Classes';
        $data['main_content'] = 'pages/classes';  // races view works the same for classes
        $this->load->view('template', $data);
    }

    public function className($class = NULL) {
        if (!is_null($class)) {
            $this->_addClassData($class);
        }
    }

    private function _addClassData($class) {
        $this->load->model('class_model');
        $data['title'] = $class;
        $data['class'] = $this->class_model->getClasses($class);
        $data['main_content'] = 'pages/class_view';
        $this->load->view('template', $data);
    }

}
?>