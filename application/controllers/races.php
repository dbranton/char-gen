<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Races extends CI_Controller {

    /*public function __construct() {
        parent::__construct();
    }*/

    public function index() {
        $this->load->model('race_model');
        $data['races'] = $this->race_model->getRaces();
        $data['title'] = 'Races';
        $data['main_content'] = 'pages/races';
        $this->load->view('template', $data);
    }

    public function dwarf() {
        $race = 'Dwarf';
        $this->_addRaceData($race);
    }

    public function elf() {
        $race = 'Elf';
        $this->_addRaceData($race);
    }

    public function halfling() {
        $race = 'Halfling';
        $this->_addRaceData($race);
    }

    public function human() {
        $race = 'Human';
        $this->_addRaceData($race);
    }

    private function _addRaceData($race) {
        $this->load->model('race_model');
        $data['race'] = $this->race_model->getRaces($race);
        $data['title'] = $race;
        $data['main_content'] = 'pages/race_view';
        $this->load->view('template', $data);
    }

}
?>