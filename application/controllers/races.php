<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Races extends CI_Controller {

    /*public function __construct() {
        parent::__construct();
    }*/

    public function index() {
        $this->load->model('race_model');
        $data['races'] = $this->race_model->getRaces();
        $data['title'] = 'Races';
        $data['main_content'] = 'pages/races_view';
        $data['type'] = '';
        $this->load->view('template', $data);
    }

    public function raceName($class = NULL) {
        if (!is_null($class)) {
            $this->_addRaceData($class);
        }
    }

    private function _addRaceData($race) {
        $this->load->model('race_model');
        $data['race'] = $this->race_model->getRaces($race);
        $data['title'] = $race;
        $data['main_content'] = 'pages/race_view';
        $data['type'] = '';
        $this->load->view('template', $data);
    }

}
?>