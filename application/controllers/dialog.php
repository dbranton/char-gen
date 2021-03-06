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
    public function raceDialog() {
        $this->load->view('partials/dialog_race');
    }

    public function classDialog() {
        $data['left_col'] = 'partials/dialog_list_col';
        $data['right_col'] = 'partials/dialog_trait_feature';
        $data['id'] = 'classModal.html';
        $this->load->view('partials/dialog_template', $data);
    }

    public function background() {
        $data['left_col'] = 'partials/dialog_list_col';
        $data['right_col'] = 'partials/dialog_background';
        $this->load->view('partials/dialog_template', $data);
    }

    public function spellDialog() {
        $this->load->view('partials/dialog_spells');
    }

    public function multipleDialog() {
        $this->load->view('partials/dialog_features');
    }

    public function summary() {
        $this->load->view('partials/dialog_summary');
    }

    public function newChar() {
        $this->load->view('partials/dialog_new_character');
    }
    // included in character_generator_view (or should it?)
    /*public function abilityscore() {
        $this->load->view('ability_dialog');
    }

    */

}

?>