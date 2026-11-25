(function ($) {
    "use strict";
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/behub-workspace-tab.default', ($scope) => {
            let $tabs = $scope.find('.tab-title .workspace-tab-item');
            let $contents = $scope.find('.tab-content');
            let $contentsItem = $scope.find('.workspace-tab-content-item');
            let $carousels = $('.behub-carousel', $scope);

            // Setup
            $tabs.first().addClass("active");
            $contentsItem.first().addClass("active");

            $tabs.on('click', function (e) {
                e.preventDefault();
                $tabs.removeClass('active');
                $contentsItem.removeClass('active');
                $(this).addClass('active');
                let id = $(this).attr('data-setting-key');
                $contents.find('#' + id).addClass('active');
                $carousels.slick('refresh');
            });


            let $check = $('.workspace-slideshow', $scope);
            if ($check.length > 0) {
                let $carousel = $('.behub-carousel', $scope);
                if ($carousel.length > 0) {
                    let rtl = $('body').hasClass('rtl');
                    $carousel.slick({
                        rtl: rtl,
                        arrows: true,
                        dot: false,
                        infinite: true,
                        speed: 500,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 5000,
                        lazyLoad: 'ondemand',
                    });
                }
            }
        });
    });

})(jQuery);



