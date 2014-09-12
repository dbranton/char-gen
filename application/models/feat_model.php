<?php

class Feat_model extends CI_Model {

    public function getFeats() {
        $sql = "SELECT * FROM feats_table WHERE active = '1' ORDER BY name ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $character = array();
            foreach ($query->result() as $row) {
                $feats['name'] = $row->name;
                $feats['desc'] = $row->description;
                $feats['prereq'] = $row->prerequisite;
                $feats['benefit'] = $row->benefit;
                array_push($character, $feats);
            }
            return $character;
        }
    }

}

?>