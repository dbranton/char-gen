<!--<div ng-app="generator" ng-controller="CharGen">-->
    <div class="modal-header">
        <h4 class="modal-title">{{title}}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-5">
                <input type="text" class="form-control" ng-model="searchText" placeholder="Search..." />
                <div class="list-group">
                    <a ng-repeat="race in subvalues | filter: searchText" ng-click="showDescription(this, race)" class="list-group-item" ng-class="{true:'active', false:''}[$index==selectedIndex]" href="">
                        {{race.subrace.name}}
                    </a>
                </div>
                <!--<accordion close-others="false">
                    <accordion-group heading="{{value.name}}" ng-repeat="value in values">
                        <ul class="selection dropdown-menu">
                            <li ng-repeat="subrace in value.subraces" ng-class="{true:'active', false:''}[$index==selectedIndex]" ng-click="showDescription(this, value, subrace)">
                                <a href="">{{subrace.name}}</a>
                            </li>
                        </ul>
                    </accordion-group>
                </accordion>-->
                <!--<ul class="selection">
                    <li class="ui-widget-content" ng-repeat="value in values" ng-click="showDescription(this)">
                        {{value.name}}
                    </li>
                </ul>-->
            </div>	<!-- end span -->
            <div class="col-md-7">
                <h3>{{featureType}}</h3>
                <dl ng-hide="!tempRace">
                    <!--<dt>Ability Score Increase</dt>
                    <dd>{{race_aba}} {{subrace_aba}}</dd>-->
                    <dt>Size</dt>
                    <dd>{{size}}</dd>
                    <dt>Speed</dt>
                    <dd>{{speed}}</dd>
                    <!--<dt>Languages</dt>
                    <dd>{{languages}}</dd>-->
                    <div ng-repeat="trait in traits">
                        <dt>{{trait.name}}</dt>
                        <dd>{{trait.benefit}}</dd>
                    </div>
                </dl>
            </div> <!-- end span -->
        </div>
    </div>
    <div class="modal-footer">
        <button ng-click="done()" class="btn btn-primary">Done</button>
        <button ng-click="close()" class="btn btn-default">Cancel</button>
    </div>