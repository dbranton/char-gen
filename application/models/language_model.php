<?php

    class Language_model extends CI_Model {

        public function getLanguages() {
            $sql = "SELECT * FROM language_table WHERE type = 'standard' ORDER BY name ASC";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $character = array();
                foreach ($query->result() as $row) {
                    $language['name'] = $row->name;
                    array_push($character, $language);
                }
                return $character;
            }
        }

    }

?>