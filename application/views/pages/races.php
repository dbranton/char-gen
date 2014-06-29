<div class="list-group">
<?php if (is_array($races)):
    for ($i=0; $i<count($races); $i++) { ?>
    <a href="races/<?php echo $races[$i]['name']; ?>" class="list-group-item">
        <h3 class="list-group-item-heading"><?php echo $races[$i]['name']; ?></h3>
        <p class="list-group-item-text"><?php echo $races[$i]['desc']; ?></p>
    </a>
    <?php }
endif; ?>
</div>