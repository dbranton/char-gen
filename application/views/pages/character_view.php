<div class="col-md-6">
    <?php
    //var_dump($character);

    if (is_array($character)) {
        echo '<p><b>Level ' . $character['level'] . ' ' . $character['race'] . ' ' . $character['class'] . '</b></p>';
        echo '<p><b>AC</b> ' . $character['armor_class'] . '</p>';
        echo '<p><b>Hit Points</b> ' . $character['hit_points'] . ' (1d' . $character['hit_dice'] . ' Hit Die)</p>';
        echo '<p><b>Proficiency Bonus</b> ' . '+' . $character['proficiency_bonus'] . '</p>';
        echo '<p><b>Speed</b> ' . $character['speed'] . ' ft.</p>';
        echo '<p><b>Languages</b> ' . $character['languages'] . '</p>';
    }
    ?>
    <fieldset>
        <legend class="small-legend">Ability Scores</legend>
        <table class="table table-condensed">
            <tbody>
                <tr>
                    <th>Strength</th>
                    <td><?php echo $character['strength']; ?></td>
                </tr>
                <tr>
                    <th>Dexterity</th>
                    <td><?php echo $character['dexterity']; ?></td>
                </tr>
                <tr>
                    <th>Constitution</th>
                    <td><?php echo $character['constitution']; ?></td>
                </tr>
                <tr>
                    <th>Intelligence</th>
                    <td><?php echo $character['intelligence']; ?></td>
                </tr>
                <tr>
                    <th>Wisdom</th>
                    <td><?php echo $character['wisdom']; ?></td>
                </tr>
                <tr>
                    <th>Charisma</th>
                    <td><?php echo $character['charisma']; ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    <fieldset>
        <legend class="small-legend">Skills (Add Proficiency Bonus To Related Ability Checks)</legend>
        <p><?php echo $character['skills']; ?></p>
    </fieldset>
    <?php
    if (is_array($character['traits'])) {
        echo '<h3>Racial Traits</h3>';
        //var_dump($character['traits']);
        foreach ($character['traits'] as $trait) {
            echo '<p><b>' . $trait['name'] . '</b>: ' . $trait['description'] . '</p>';
        }
    }?>
</div>
<div class="col-md-6">
    <?php
    if (is_array($character['features'])) {
        echo '<h3>Class Features</h3>';
        //var_dump($character['features']);
        foreach ($character['features'] as $feature) {
            echo '<p><b>' . $feature['name'] . '</b>: ' . $feature['benefit'] . '</p>';
        }
    }
    ?>
    <h3>Background: <?php echo $character['background']['name']; ?></h3>
    <p><b><?php echo $character['background']['trait_name']; ?>.</b> <?php echo $character['background']['trait_desc']; ?></p>
</div>