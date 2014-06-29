<?php
    $this->load->view('templates/header', $title);
    $this->load->view('templates/nav');
    $this->load->view($main_content);
    $this->load->view('templates/footer');
?>