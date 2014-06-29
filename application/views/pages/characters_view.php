<div class="panel panel-default">
    <div class="panel-heading">Your Characters</div>
    <!--<div class="panel-body">
    </div>-->
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Level</th>
            <th>Race</th>
            <th>Class</th>
            <!--<th>Subclass</th>-->
        </tr>
        </thead>
        <tbody>
        <?php
        //var_dump($characters);

        if (is_array($characters)) {
            for ($i=0; $i<count($characters); $i++) {
                echo '<tr>';
                echo '<td>' . anchor('user/character/' . $characters[$i]['id'], $characters[$i]['name']) . '</td>';
                echo '<td>' . $characters[$i]['level'] . '</td>';
                echo '<td>' . $characters[$i]['race'] . '</td>';
                echo '<td>' . $characters[$i]['class'] . '</td>';
                echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>