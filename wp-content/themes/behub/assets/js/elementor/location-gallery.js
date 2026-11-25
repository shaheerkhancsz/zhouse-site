(function ($) {
    "use strict";
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/behub-location-gallery.default', ($scope) => {
            let $check = $('.location-slideshow', $scope);
            if ($check.length > 0) {
                let $carousel = $('.behub-carousel', $scope);
                if ($carousel.length > 0) {
                    let rtl = $('body').hasClass('rtl');
                    $carousel.slick({
                        rtl: rtl,
                        arrows: true,
                        dots: true,
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



