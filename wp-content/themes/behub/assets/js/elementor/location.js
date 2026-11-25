(function ($) {
    "use strict";
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/behub-location.default', ($scope) => {

            let currentIsotope = $scope.find('.isotope-grid');
            if (currentIsotope.length) {
                let objisotope = {filter: '*'};
                currentIsotope.isotope(objisotope);
                $scope.find('.elementor-location__filters li').on('click', function () {
                    $(this).parents('ul.elementor-location__filters').find('li.elementor-location__filter').removeClass('elementor-active');
                    $(this).addClass('elementor-active');
                    let selector = $(this).attr('data-filter');
                    currentIsotope.isotope({filter: selector});
                });
                currentIsotope.imagesLoaded(function () {
                    currentIsotope.isotope('layout');
                });
            }

            let $carousel = $('.behub-carousel', $scope);
            if ($carousel.length > 0) {
                let data = $carousel.data('settings'),
                    rtl = $('body').hasClass('rtl');
                if (data['layout'] === '2'){
                    $carousel.slick(
                        {
                            rtl: rtl,
                            dots: data.navigation == 'both' || data.navigation == 'dots' ? true : false,
                            arrows: data.navigation == 'both' || data.navigation == 'arrows' ? true : false,
                            infinite: data.loop,
                            speed: 300,
                            slidesToShow: parseFloat(data.items),
                            autoplay: data.autoplay,
                            autoplaySpeed: data.autoplaySpeed,
                            slidesToScroll: 1,
                            lazyLoad: 'ondemand',
                            responsive: [
                                {
                                    breakpoint: parseFloat(data.breakpoint_laptop),
                                    settings: {
                                        slidesToShow: parseFloat(data.items_laptop),
                                    }
                                },
                                {
                                    breakpoint: parseFloat(data.breakpoint_tablet_extra),
                                    settings: {
                                        slidesToShow: parseFloat(data.items_tablet_extra),
                                    }
                                },
                                {
                                    breakpoint: parseFloat(data.breakpoint_tablet),
                                    settings: {
                                        slidesToShow: parseFloat(data.items_tablet),
                                    }
                                },
                                {
                                    breakpoint: parseFloat(data.breakpoint_mobile_extra),
                                    settings: {
                                        slidesToShow: parseFloat(data.items_mobile_extra),
                                    }
                                },
                                {
                                    breakpoint: parseFloat(data.breakpoint_mobile),
                                    settings: {
                                        slidesToShow: parseFloat(data.items_mobile),
                                    }
                                }
                            ]
                        }
                    );
                }else {
                    $carousel.slick(
                        {
                            rtl: rtl,
                            dots: data.navigation == 'both' || data.navigation == 'dots' ? true : false,
                            arrows: data.navigation == 'both' || data.navigation == 'arrows' ? true : false,
                            infinite: data.loop,
                            speed: 300,
                            slidesToShow: parseInt(data.items),
                            autoplay: data.autoplay,
                            autoplaySpeed: data.autoplaySpeed,
                            slidesToScroll: 1,
                            lazyLoad: 'ondemand',
                            responsive: [
                                {
                                    breakpoint: parseInt(data.breakpoint_laptop),
                                    settings: {
                                        slidesToShow: parseInt(data.items_laptop),
                                    }
                                },
                                {
                                    breakpoint: parseInt(data.breakpoint_tablet_extra),
                                    settings: {
                                        slidesToShow: parseInt(data.items_tablet_extra),
                                    }
                                },
                                {
                                    breakpoint: parseInt(data.breakpoint_tablet),
                                    settings: {
                                        slidesToShow: parseInt(data.items_tablet),
                                    }
                                },
                                {
                                    breakpoint: parseInt(data.breakpoint_mobile_extra),
                                    settings: {
                                        slidesToShow: parseInt(data.items_mobile_extra),
                                    }
                                },
                                {
                                    breakpoint: parseInt(data.breakpoint_mobile),
                                    settings: {
                                        slidesToShow: parseInt(data.items_mobile),
                                    }
                                }
                            ]
                        }
                    );
                }
            }

            let $total = $scope.find('.elementor-location__filters .total').text();
            $scope.find('.elementor-location__filters .all .count').text($total);
        });
    });

})(jQuery);



