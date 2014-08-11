<!--<div ng-app="generator" ng-controller="CharGen">-->
    <div class="modal-header">
        <h4 class="modal-title">{{title}}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <p>{{spellsLeft - tempCantrips.length}} Spells Left:</p>
            <div class="col-md-5">
                <div class="list-group">
                    <a ng-repeat="spell in values" ng-click="showDescription(this)" class="list-group-item" ng-class="{'active': spell.active}" href="">
                        {{spell.name}}
                    </a> <!--ng-class="{true:'active', false:''}[$index==selectedIndex]"-->
                </div>
            </div>	<!-- end span -->
            <div class="col-md-7">
                <h3>{{selectedCantrip.name}}</h3>
                <ul class="list-unstyled" ng-hide="!selectedCantrip">
                    <li><b>Casting Time:</b> {{selectedCantrip.casting_time}}</li>
                    <li><b>Range:</b> {{selectedCantrip.range}}</li>
                    <li><b>Components:</b> {{selectedCantrip.components}}</li>
                    <li><b>Duration:</b> {{selectedCantrip.duration}}</li>
                </ul>
                <p ng-bind-html="selectedCantrip.desc"></p>
            </div> <!-- end span -->
        </div>
    </div>
    <div class="modal-footer">
        <button ng-click="done()" ng-disabled="disabled" class="btn btn-primary">Done</button>
        <button ng-click="close()" class="btn btn-default">Cancel</button>
    </div>