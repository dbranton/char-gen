<script type="text/ng-template" id="backgroundModal.html">
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
                <dl ng-hide="!tempBackground">
                    <div>
                        <dt>{{traitName}}</dt>
                        <dd>{{traitDesc}}</dd>
                    </div>
                    <div>
                        <dt>Skills</dt>
                        <dd>
                            {{skills}}
                            <!--<ul>
                                <li ng-repeat="skill in skills">{{skill}}</li>
                            </ul>-->
                        </dd>
                    </div>
                    <div ng-show="tools">
                        <dt>Tools</dt>
                        <dd>
                            {{tools}}
                        </dd>
                    </div>
                    <div ng-show="languages">
                        <dt>Languages</dt>
                        <dd>
                            {{languages}}
                        </dd>
                    </div>
                </dl>
            </div> <!-- end span -->
        </div>
    </div>
    <div class="modal-footer">
        <button ng-click="done()" class="btn btn-primary">Done</button>
        <button ng-click="close()" class="btn btn-default">Cancel</button>
    </div>
</script>