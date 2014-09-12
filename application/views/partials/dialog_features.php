
<div class="modal-header">
    <h4 class="modal-title">{{title}}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <p>{{max - tempFeatures.length}} Features Left:</p>
        <div class="col-md-5">
            <div class="list-group">
                <a href="" class="list-group-item" ng-repeat="value in values" ng-class="{'active': value.active}" ng-click="showDescription(this)">
                    {{value.name}}
                </a>
            </div>
        </div>
        <div class="col-md-7">
            <dl>
                <dt>{{selectedFeature.name}}</dt>
                <dd ng-bind-html="selectedFeature.desc"></dd>
            </dl>
        </div> <!-- end span -->
    </div>
</div>
<div class="modal-footer">
    <button ng-click="done()" class="btn btn-primary" ng-disabled="disabled">Done</button>
    <button ng-click="close()" class="btn btn-default">Cancel</button>
</div>