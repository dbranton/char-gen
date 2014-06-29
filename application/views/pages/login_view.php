<div ng-app>
    <?php echo validation_errors(); ?>
    <div ng-controller="User">
        <div class="well">
            <?php echo form_open('user/login', array('class' => 'form-horizontal')); ?>
                <div class="form-group">
                    <label for="username" class="col-lg-2 control-label">Username: </label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" ng-required="true" size="20" id="username" name="username" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="col-lg-2 control-label">Password:</label>
                    <div class="col-lg-10">
                        <input type="password" class="form-control" ng-required="true" size="20" id="password" name="password" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input class="btn btn-primary" type="submit" value="Login" />
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>