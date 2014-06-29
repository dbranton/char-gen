<?php

class Dialog extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        //$data['left_col'] = 'dialog_list_col';
        //$data['right_col'] = 'dialog_trait_feature';
        //$this->load->view('dialog_template', $data);
    }

    // included in character_generator_view
    /*public function abilityscore() {
        $this->load->view('ability_dialog');
    }

    public function raceDialog() {
        $this->load->view('dialog_race');
    }

    public function classDialog() {
        $data['left_col'] = 'dialog_list_col';
        $data['right_col'] = 'dialog_trait_feature';
        $data['id'] = 'classModal.html';
        $this->load->view('dialog_template', $data);
    }

    public function background() {
        $data['left_col'] = 'dialog_list_col';
        $data['right_col'] = 'dialog_background';
        $this->load->view('dialog_template', $data);
    }*/

}

?>