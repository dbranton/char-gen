<div class="list-group">
<?php if (is_array($classes)):
    for ($i=0; $i<count($classes); $i++) { ?>
    <a href="classes/<?php echo $classes[$i]['name']; ?>" class="list-group-item">
        <h3 class="list-group-item-heading"><?php echo $classes[$i]['name']; ?></h3>
        <p class="list-group-item-text"><?php echo $classes[$i]['desc']; ?></p>
    </a>
    <?php }
        endif; ?>
</div>