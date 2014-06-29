<?php

class Items extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['items'] = $this->item_model->get_items();
        $this->master_view('items/index', $data);
    }
}
