<?php
if (is_array($feats)):
    //var_dump($feats);
    foreach ($feats as $feat) {
        echo '<div class="well well-sm">';
        echo '<h4>' . $feat['name'] . '</h4>';
        //echo '<i>' . $feat['category'] . ' feat</i>';
        if ($feat['desc']) {
            echo '<p>' . $feat['desc'] . '</p>';
        }
        if ($feat['prereq']) {
            echo '<p><b>Prerequisite: </b>' . $feat['prereq'] . '</p>';
        }
        if ($feat['benefit']) {
            //echo '<p><b>Benefit: </b>' . $feat['benefit'] . '</p>';
            echo '<div>' . $feat['benefit'] . '</div>';
        }
        if ($feat['effect']) {
            echo '<p><b>Effect: </b>' . $feat['effect'] . '</p>';
        }
        echo '<a class="pull-right" href="#top">Back to top</a>';
        echo '<div class="clearfix"></div>';
        echo '</div>';
    }
?>
<?php endif; ?>