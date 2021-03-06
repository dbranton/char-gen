
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