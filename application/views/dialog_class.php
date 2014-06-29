<script type="text/ng-template" id="classModal.html">
    <div class="modal-header">
        <h4 class="modal-title">{{title}}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-5">
                <?php $this->load->view('dialog_list_col'); ?>
            </div>
            <div class="col-md-7">
                <h4>{{featureType}}</h4>
                <div ng-show="traits">
                    <h5>{{traitsTitle}}</h5>
                    <ul class="list-unstyled">
                        <li ng-repeat="trait in traits">
                            <b>{{trait.name}}:</b> {{trait.benefit}}
                            <!--<span ng-bind="trait.benefit"></span>-->
                        </li>
                    </ul>
                    <h5>{{traits2Title}}</h5>
                    <ul class="list-unstyled">
                        <li ng-repeat="trait in traits2">
                            <b>{{trait.name}}:</b> {{trait.benefit}}
                            <!--<span ng-bind="trait.benefit"></span>-->
                        </li>
                    </ul>
                </div>
                <div ng-show="features">
                    <dl>
                        <div ng-repeat="feature in features">
                            <dt>{{feature.name}}</dt>
                            <dd ng-bind-html="feature.benefit"></dd>
                        </div>
                    </dl>
                </div>
            </div> <!-- end span -->
        </div>
    </div>
    <div class="modal-footer">
        <button ng-click="done()" class="btn btn-primary">Done</button>
        <button ng-click="close()" class="btn btn-default">Cancel</button>
    </div>
</script>