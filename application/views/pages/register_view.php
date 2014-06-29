<div ng-app>
    <?php echo validation_errors('<div class="text-danger">','</div>'); ?>
    <div ng-controller="User">
        <div class="well">
            <?php echo form_open("user/register", array('class' => 'form-horizontal', 'name' => 'register-name', 'novalidate' => 'true')); ?>
            <div class="form-group" ng-class="{true:'has-error', false:''}[register-name.$error]">
                <label for="username" class="col-lg-2 control-label">User Name:</label>
                <div class="col-lg-10">
                    <input type="text" class="form-control" required id="username" name="username" value="<?php echo set_value('username'); ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="col-lg-2 control-label">Your Email:</label>
                <div class="col-lg-10">
                    <input type="text" class="form-control" id="email" name="email" value="<?php echo set_value('email'); ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-lg-2 control-label">Password:</label>
                <div class="col-lg-10">
                    <input type="password" class="form-control" required id="password" name="password" value="<?php echo set_value('password'); ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="con_password" class="col-lg-2 control-label">Confirm Password:</label>
                <div class="col-lg-10">
                    <input type="password" class="form-control" required id="con_password" name="con_password" value="<?php echo set_value('con_password'); ?>" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" class="btn btn-primary" value="Submit" />
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>