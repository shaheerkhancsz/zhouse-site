(function ($) {
    "use strict";
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/behub-location-packages.default', ($scope) => {

            let $dropdown =  $scope.find('.button-detail');
            $dropdown.on('click', function (e) {
                e.preventDefault();
                $dropdown.toggleClass('toggled-on');
                $dropdown.siblings('.detail').stop().slideToggle();
            });
        });
    });

})(jQuery);



