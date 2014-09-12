<div class="list-group">
<?php if (is_array($races)):
    foreach ($races as $race): ?>
    <a href="races/<?php echo $race['name']; ?>" class="list-group-item">
        <h3 class="list-group-item-heading"><?php echo $race['name']; ?></h3>
        <p class="list-group-item-text"><?php echo $race['desc']; ?></p>
    </a>
    <?php endforeach;
endif; ?>
</div>