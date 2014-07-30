<?php

class Skill_model extends CI_Model {

    public function getSkills() {
        $sql = "SELECT * FROM skills_table ORDER BY name ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $skills = array();
            foreach ($query->result() as $row) {
                $skill['name'] = $row->name;
                $skill['ability'] = $row->ability;
                array_push($skills, $skill);
            }
            return $skills;
        }
    }

}

?>