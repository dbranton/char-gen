<p>Choosing a background is one of four key decisions you make about your character, providing you with important story cues about his or her identity. In addition, that background includes a special trait and suggestions for starting skills and equipment.</p>
<?php
if (is_array($backgrounds)):
    for ($i=0; $i<count($backgrounds); $i++) { ?>
        <div class="well well-sm">
            <h4>
                <?php echo $backgrounds[$i]['name']; ?>
            </h4>
            <div>
                <p><?php echo $backgrounds[$i]['desc']; ?></p>
                <p><b>Feature: <?php echo $backgrounds[$i]['trait_name']; ?>:</b>
                <?php echo $backgrounds[$i]['trait_desc']; ?></p>
                <p><b>Skills:</b> <?php echo $backgrounds[$i]['skills']; ?></p>
                <?php if ($backgrounds[$i]['tools']): ?>
                    <p><b>Tools:</b> <?php echo $backgrounds[$i]['tools']; ?></p>
                <?php endif; ?>
                <?php if ($backgrounds[$i]['languages']): ?>
                    <p><b>Languages:</b> <?php echo $backgrounds[$i]['language_desc']; ?></p>
                <?php endif; ?>
                <a class="pull-right" href="#top">Back to top</a>
                <div class="clearfix"></div>
            </div>
        </div>
<?php }
endif; ?>