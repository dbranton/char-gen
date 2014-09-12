<table class="table table-hover">
    <thead>
        <tr>
            <th>Class</th>
            <th>Description</th>
            <th>Hit Die</th>
            <th>Primary Ability</th>
            <th>Saving Throw Proficiencies</th>
        </tr>
    </thead>
    <tbody>
    <?php if (is_array($classes)):
    foreach ($classes as $class): ?>
    <tr>
        <td><a href="classes/<?php echo $class['name']; ?>"><?php echo $class['name']; ?></a></td>
        <td><?php echo $class['short_desc']; ?></td>
        <td><?php echo 'd' . $class['hit_dice']; ?></td>
        <td><?php echo $class['primary_ability']; ?></td>
        <td><?php echo $class['saving_throws']; ?></td>
    </tr>
    <?php endforeach;
    endif; ?>
    </tbody>
</table>