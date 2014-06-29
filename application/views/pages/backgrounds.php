<p>Choosing a background is one of four key decisions you make about your character, providing you with important story cues about his or her identity. In addition, that background includes a special trait and suggestions for starting skills and equipment.</p>
<?php
if (is_array($backgrounds))://var_dump($backgrounds);?>
    <div class="panel-group">
    <?php for ($i=0; $i<count($backgrounds); $i++) { ?>
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo '<h4 class="panel-title"><a class="collapsed" data-toggle="collapse" data-parent="#backgrounds" href="#collapse' . $i . '">' . $backgrounds[$i]['name'] . '</a></h4>'; ?></div>
            <div id="collapse<?php echo $i; ?>" class="panel-collapse collapse">
                <div class="panel-body">
                    <p><?php echo $backgrounds[$i]['desc']; ?></p>
                    <p><b>Trait—<?php echo $backgrounds[$i]['trait_name']; ?>:</b>
                    <?php echo $backgrounds[$i]['trait_desc']; ?></p>
                    <p><b>Skills:</b> <?php echo $backgrounds[$i]['skills']; ?></p>
                    <?php if ($backgrounds[$i]['tools']): ?>
                        <p><b>Tools:</b> <?php echo $backgrounds[$i]['tools']; ?></p>
                    <?php endif; ?>
                    <?php if ($backgrounds[$i]['languages']): ?>
                        <p><b>Languages:</b> <?php echo $backgrounds[$i]['language_desc']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php } ?>
    </div>
<?php endif; ?>