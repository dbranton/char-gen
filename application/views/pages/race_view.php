<?php
    echo '<p>' . $race['desc'] . '</p>';
    echo '<ul>';
    //echo '<li><b>Ability Score Increase</b>: ' . $race['ability_score_adjustment'] . '</li>';
    echo '<li><b>Size</b>: ' . $race['size'] . '</li>';
    echo '<li><b>Speed</b>: ' . $race['speed'] . '</li>';
    if (is_array($race['traits'])) {
        foreach ($race['traits'] as $traitArray) {
            echo '<li><b>' . $traitArray['name'] . '</b>: ' . $traitArray['description'] . '</li>';
        }
    }
    //echo '<li><b>Languages</b>: ' . $race['language_desc'] . '</li>';
    echo '</ul>';
    if (is_array($race['subraces'])) {
        for ($i=0; $i<count($race['subraces']); $i++) {
            $subrace = $race['subraces'][$i];
            echo '<h3>' . $subrace['name'] . '</h3>';
            echo '<p>' . $subrace['desc'] . '</p>';
            if (is_array($subrace['traits'])) {
                echo '<ul>';
                //echo '<li><b>Ability Score Increase</b>: ' . $subrace['ability_score_adjustment'] . '</li>';
                foreach ($subrace['traits'] as $traitArray) {
                    echo '<li><b>' . $traitArray['name'] . '</b>: ' . $traitArray['description'] . '</li>';
                }
                echo '</ul>';
            }
        }
    }
?>