<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Equipment extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model('equipment_model');
        $data['armor'] = $this->equipment_model->getArmors();
        $data['weapon'] = $this->equipment_model->getWeapons();
        $data['title'] = 'Equipment';
        $data['main_content'] = 'pages/equipment_view';  // races view works the same for classes
        $data['type'] = '';
        $this->load->view('template', $data);
    }

}
?>