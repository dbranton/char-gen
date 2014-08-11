<div ng-app="generator" ng-controller="CharGen">
    <div class="modal-header">
        <h3>{{title}}</h3>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <div class="span5">
                <input type="text"class="span12" ng-model="searchText" placeholder="Search..." />
                <ul class="nav nav-tabs nav-stacked">
                    <li ng-repeat="value in values | filter: searchText" ng-class="{true:'active', false:''}[$index==selectedIndex]" ng-click="showDescription(this)">
                        <a href="">
                            {{value.name}}
                        </a>
                    </li>
                </ul>
            </div>	<!-- end span -->
            <div class="span7">
                <p>{{description}}</p>
                <h4>{{featureType}}</h4>
                <dl>
                    <div ng-repeat="trait in traits">
                        <dt>{{trait.name}}</dt>
                        <dd ng-bind-html-unsafe="trait.benefit"></dd>
                    </div>
                </dl>
                <dl>
                    <div ng-repeat="feature in features">
                        <dt>{{feature.name}}</dt>
                        <dd ng-bind-html-unsafe="feature.benefit"></dd>
                    </div>
                </dl>
            </div> <!-- end span -->
        </div>
    </div>
    <div class="modal-footer">
        <button ng-click="done()" class="btn btn-primary">Done</button>
        <button ng-click="close()" class="btn">Cancel</button>
    </div>
</div>