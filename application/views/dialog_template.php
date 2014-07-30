
    <div class="modal-header">
        <h4 class="modal-title">{{title}}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-5">
                <?php $this->load->view($left_col); ?>
            </div>
            <div class="col-md-7">
                <!--<p>{{description}}</p>-->
                <h4>{{featureType}}</h4>
                <?php $this->load->view($right_col); ?>
            </div> <!-- end span -->
        </div>
    </div>
    <div class="modal-footer">
        <button ng-click="done()" class="btn btn-primary">Done</button>
        <button ng-click="close()" class="btn btn-default">Cancel</button>
    </div>