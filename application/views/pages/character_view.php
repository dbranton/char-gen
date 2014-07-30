<div class="col-md-6">
    <?php
    //var_dump($character);

    if (is_array($character)) {
        echo '<p><b>Level ' . $character['level'] . ' ' . $character['race'] . ' ' . $character['class'] . '</b></p>';
        echo '<p><b>Armor Class</b> ' . $character['armor_class'] . '</p>';
        echo '<p><b>Hit Points</b> ' . $character['hit_points'] . ' (1d' . $character['hit_dice'] . ' Hit Die)</p>';
        echo '<p><b>Initiative</b> ' . $character['initiative'] . '</p>';
        echo '<p><b>Speed</b> ' . $character['speed'] . ' ft.</p>';
        echo '<p><b>Proficiency Bonus</b> ' . '+' . $character['proficiency_bonus'] . '</p>';
        echo '<p><b>Proficiencies</b> ' . $character['proficiencies'] . '</p>';
        echo '<p><b>Languages</b> ' . $character['languages'] . '</p>';
        echo '<p><b>Passive Wisdom (Perception)</b> ' . $character['senses'] . '</p>';
    }
    ?>
    <fieldset>
        <legend class="small-legend">Ability Scores</legend>
        <table class="table table-condensed">
            <thead>
                <th>Ability</th>
                <th>Base</th>
                <th>Mod</th>
                <th><abbr title="Saving Throws">ST</abbr></th>
            </thead>
            <tbody>
                <tr>
                    <td>Strength</td>
                    <td><?php echo $character['strength']; ?></td>
                    <td><?php echo $character['str_mod']; ?></td>
                    <td><?php echo $character['str_st']; ?></td>
                </tr>
                <tr>
                    <td>Dexterity</td>
                    <td><?php echo $character['dexterity']; ?></td>
                    <td><?php echo $character['dex_mod']; ?></td>
                    <td><?php echo $character['dex_st']; ?></td>
                </tr>
                <tr>
                    <td>Constitution</td>
                    <td><?php echo $character['constitution']; ?></td>
                    <td><?php echo $character['con_mod']; ?></td>
                    <td><?php echo $character['con_st']; ?></td>
                </tr>
                <tr>
                    <td>Intelligence</td>
                    <td><?php echo $character['intelligence']; ?></td>
                    <td><?php echo $character['int_mod']; ?></td>
                    <td><?php echo $character['int_st']; ?></td>
                </tr>
                <tr>
                    <td>Wisdom</td>
                    <td><?php echo $character['wisdom']; ?></td>
                    <td><?php echo $character['wis_mod']; ?></td>
                    <td><?php echo $character['wis_st']; ?></td>
                </tr>
                <tr>
                    <td>Charisma</td>
                    <td><?php echo $character['charisma']; ?></td>
                    <td><?php echo $character['cha_mod']; ?></td>
                    <td><?php echo $character['cha_st']; ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    <fieldset>
        <legend class="small-legend">Skills</legend>
        <ul class="list-inline">
            <li>Acrobatics <?php echo $character['acrobatics']; ?></li>
            <li>Animal Handling <?php echo $character['acrobatics']; ?></li>
            <li>Arcana <?php echo $character['arcana']; ?></li>
            <li>Athletics <?php echo $character['athletics']; ?></li>
            <li>Deception <?php echo $character['deception']; ?></li>
            <li>History <?php echo $character['history']; ?></li>
            <li>Insight <?php echo $character['insight']; ?></li>
            <li>Intimidation <?php echo $character['intimidation']; ?></li>
            <li>Investigation <?php echo $character['investigation']; ?></li>
            <li>Medicine <?php echo $character['medicine']; ?></li>
            <li>Nature <?php echo $character['nature']; ?></li>
            <li>Perception <?php echo $character['perception']; ?></li>
            <li>Performance <?php echo $character['performance']; ?></li>
            <li>Persuasion <?php echo $character['persuasion']; ?></li>
            <li>Religion <?php echo $character['religion']; ?></li>
            <li>Sleight of Hand <?php echo $character['sleight_of_hand']; ?></li>
            <li>Stealth <?php echo $character['stealth']; ?></li>
            <li>Survival <?php echo $character['survival']; ?></li>
        </ul>
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