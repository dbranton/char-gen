<?php

    class Background_model extends CI_Model {

        public function getBackgrounds() {
            $sql = "SELECT * FROM background_table WHERE active = '1' ORDER BY name ASC";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $character = array();
                foreach ($query->result() as $row) {
                    $background['name'] = $row->name;
                    $background['desc'] = $row->description;
                    $background['trait_name'] = $row->trait_name;
                    $background['trait_desc'] = $row->trait_desc;
                    //$this->_getSkills($row->id); //$row->skills;
                    $background['skills'] = $row->skills;
                    $background['tools'] = $row->tools;
                    $background['languages'] = $row->languages;
                    $background['language_desc'] = $row->language_desc;
                    $background['skills_desc'] = $this->_getSkills($row->id); //$row->skills;
                    array_push($character, $background);
                }
                return $character;
            }
        }

        /* Use this query for phpmyadmin
         * SELECT background_table.id, background_table.name, background_table.skills, skills_table.id, skills_table.name
           FROM skills_table
           JOIN background_skills
           ON background_skills.skill_id = skills_table.id
           JOIN background_table
           ON background_table.id = background_skills.background_id
         */
        private function _getSkills($backgroundId) {
            $sql = "SELECT skills_table.*" . //features_table.*, class_features.*" .
                " FROM skills_table" .
                " JOIN background_skills" .
                " ON background_skills.skill_id = skills_table.id" .
                " JOIN background_table" .
                " ON background_table.id = background_skills.background_id" .
                " WHERE background_skills.background_id = '" . $backgroundId . "'";
            $query = $this->db->query($sql);
            $skills = array();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    array_push($skills, $row->name);
                }
            }
            return $skills;
        }
    }

?>