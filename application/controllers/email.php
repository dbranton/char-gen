<?php

    /**
     * Sends email with gmail
     */
    class Email extends CI_Controller {

        function index() {
            $data['title'] = 'Contact';
            $this->load->view('header', $data);
            $this->load->view('nav', $data);
            $this->load->view('contact');
            $this->load->view('footer');
        }

        function send() {
            $this->load->library('form_validation');

            // field name, error message, validation rules
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');

            if($this->form_validation->run() == FALSE) {
                $this->load->view('contact');   // reload view
            } else {
                // validation has passed, now send the email
                $email = $this->input->post('email');
                $comments = $this->input->post('comments');

                $this->load->library('email');
                $this->email->set_newline("\r\n");

                $this->email->from('daniel.branton@gmail.com', 'Daniel Branton');
                $this->email->to('daniel.branton@gmail.com');
                $this->email->subject('This is an email test');
                $this->email->message($comments);

                $path = $this->config->item('base_url');
                //$file = $path . '/attachments/yourInfo.txt';

                //$this->email->attach($file);

                //echo $file . '<br />';

                if ($this->email->send()) {
                    $this->load->view('signup_confirmation_view'); // will need to create this
                } else {
                    show_error($this->email->print_debugger());
                }

            }


        }
    }
?>