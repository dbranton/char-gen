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

    public function barbarian() {
        $class = 'Barbarian';
    }

    public function cleric() {
        $class = 'Cleric';
        $this->_addClassData($class);
    }

    public function fighter() {
        $class = 'Fighter';
        $this->_addClassData($class);
    }

    public function ranger() {
        $class = 'Ranger';
    }

    public function rogue() {
        $class = 'Rogue';
        $this->_addClassData($class);
    }

    public function mage() {
        $class = 'Mage';
        $this->_addClassData($class);
    }


    private function _addClassData($class) {
        $this->load->model('class_model');
        $data['title'] = $class;
        $data['class'] = $this->class_model->getClasses($class);
        $data['main_content'] = 'pages/class';
        $this->load->view('template', $data);
    }

}
?>