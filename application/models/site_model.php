<?php

    class Site_model extends CI_Model {

        function getAll() {
            //$query = $this->db->query('SELECT * FROM character_table');
            $this->db->select('name, level, race, class');
            $this->db->from('character_table');
            $this->db->where('class', 'Rogue');
            $query = $this->db->get();
            /*$sql = 'SELECT name, level, race, class FROM character_table WHERE class = ?';
            $query = $this->db->query($sql, array('Fighter'));
*/
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $data[] = $row;
                }
                return $data;
            }
        }

    }

?>