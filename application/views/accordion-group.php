<div class="accordion-group">
    <div class="accordion-heading">
        <a class="accordion-toggle" ng-click="isOpen = !isOpen">{{heading}}</a>
    </div>
    <div class="accordion-body collapse" collapse="!isOpen">
        <div class="accordion-inner" ng-transclude>

        </div>
    </div>
</div>