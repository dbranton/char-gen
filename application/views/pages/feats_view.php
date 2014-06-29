<?php
if (is_array($feats)):
    //var_dump($feats);
    foreach ($feats as $feat) {
        echo '<div class="well">';
        echo '<h4>' . $feat['name'] . '</h4>';
        echo '<i>' . $feat['category'] . ' feat</i>';
        echo '<p>' . $feat['desc'] . '</p>';
        if ($feat['prereq']) {
            echo '<p><b>Prerequisite: </b>' . $feat['prereq'] . '</p>';
        }
        if ($feat['benefit']) {
            echo '<p><b>Benefit: </b>' . $feat['benefit'] . '</p>';
        }
        if ($feat['effect']) {
            echo '<p><b>Effect: </b>' . $feat['effect'] . '</p>';
        }
        echo '</div>';
    }
?>
<?php endif; ?>