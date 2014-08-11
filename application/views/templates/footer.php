<?php
    $base_url = $this->config->item('base_url');
?>

        </div> <!-- end subwrap -->
        <footer>
            <p>Developed by <a target="_blank" href="http://danielbranton.com">Daniel Branton</a>.
                    This site is not affiliated with, endorsed, sponsored, or specifically approved by Wizards of the Coast LLC. This site may use the trademarks and other intellectual property of Wizards of the Coast LLC, which is permitted under Wizards' <a target="_blank" href="http://www.wizards.com/fankit/fantoolkitdnd.html">Fan Site Policy</a>. DUNGEONS & DRAGONS® and D&D® are trademark[s] of Wizards of the Coast and D&D® core rules, game mechanics, characters and their distinctive likenesses are the property of the Wizards of the Coast. For more information about Wizards of the Coast or any of Wizards' trademarks or other intellectual property, please visit their website at <a target="_blank" href="http://www.wizards.com">www.wizards.com</a>.</p>
        </footer>

<!--<div ng-app class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php //echo form_open('user/login', array('class' => 'form-horizontal')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Login</h4>
            </div>
            <div class="modal-body">
                <?php //echo validation_errors(); ?>
                <div ng-controller="User">
                    <div class="form-group">
                        <label for="username" class="col-lg-2 control-label">Username: </label>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" required size="20" id="username" name="username" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-lg-2 control-label">Password:</label>
                        <div class="col-lg-10">
                            <input type="password" class="form-control" required size="20" id="password" name="password" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input class="btn btn-primary" type="submit" value="Login" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>-->

       </div> <!-- end of "row" div -->
    </div> <!-- end of "wrap" div -->

    <!-- SCRIPTS -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<?php
 echo '<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.19/angular.min.js"></script>';
 echo '<script src="' . $base_url . '/assets/js/vendor/angular/angular-sanitize.js"></script>';
 if($title == "Level Up" || $title == "Character Generator") {
    echo '<script src="' . $base_url . '/assets/js/vendor/transition.js"></script>';
    echo '<script src="' . $base_url . '/assets/js/vendor/select2-3.4.3/select2.min.js"></script>';
    echo '<script src="' . $base_url . '/assets/js/vendor/angular-ui/select2.js"></script>';
 }
?>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<?php
    echo '<script src="' . $base_url . '/assets/js/vendor/bootstrap.min.js"></script>';
    //echo '<script src="' . $base_url . '/assets/js/vendor/bootstrap-modalmanager.js"></script>';
    //echo '<script src="' . $base_url . '/assets/js/vendor/bootstrap-modal.js"></script>';
    echo '<script src="' . $base_url . '/assets/js/vendor/bootbox.min.js"></script>';
    //<script src="js/vendor/bootstrap.min.js"></script>
    if($type == "character_generator") {
        echo '<script src="' . $base_url . '/assets/js/vendor/angular-ui/modal.js"></script>';  // needs to come after bootstrap
        echo '<script src="' . $base_url . '/assets/js/services/angular-local-storage.js"></script>';
        echo '<script src="' . $base_url . '/assets/js/services/character_generator.js"></script>';
        echo '<script src="' . $base_url . '/assets/js/directives/directive.js"></script>';
        echo '<script src="' . $base_url . '/assets/js/controllers/controller.js"></script>';
    } else if ($type == "user") {
        echo '<script src="' . $base_url . '/assets/js/controllers/userCtrl.js"></script>';
    }
?>
<script type="text/javascript">

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-48851623-1']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();

</script>
</body>
</html>