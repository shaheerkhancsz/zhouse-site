(function ($) {
    "use strict";
    $( document ).ready(function() {
        var panorama, viewer, container;
        var behub_panorama = $('.behub-panorama'),
            data = behub_panorama.data('settings'),
            index = behub_panorama.data('index');
        container = document.querySelector( '#container' );
        panorama = [];
        if(data.length) {
            $.each(data, function (index, value) {
                panorama[index] = new PANOLENS.ImagePanorama(value);
                $( '#viewer-'+index ).on( 'click', function (e) {
                    e.preventDefault();
                    $('.viewer-nav a.active').removeClass('active');
                    $(this).addClass('active');
                    viewer.setPanorama(panorama[index]);
                } );
            })
        }

        viewer = new PANOLENS.Viewer({container: container,controlBar:false, autoRotate: true, autoRotateSpeed: 0.3, autoRotateActivationDuration: 5000,cameraFov: 120});
        viewer.add(...panorama);
        viewer.setPanorama(panorama[index]);
    })

})
(jQuery);