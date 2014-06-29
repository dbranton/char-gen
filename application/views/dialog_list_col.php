<input type="text" class="form-control" ng-model="searchText" placeholder="Search..." />
<div class="list-group">
    <a href="" class="list-group-item" ng-repeat="value in values | filter: filterByName" ng-class="{true:'active', false:''}[$index==selectedIndex]" ng-click="showDescription(this)">
        {{value.name}}
    </a>
</div>