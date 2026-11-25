(function ($) {
    $(function () {

        // Handle Activation Settings
        handleActivationSettings();

        // Handle callback from Envato when activating the plugin
        authenticateEnvatoOAuthCallback();

        $('#wdt-envato-activation-powerful').on('click', function () {
            authenticateEnvatoOAuth()
        });

        $('#wdt-envato-deactivation-powerful').on('click', function () {
            deactivatePlugin()
        });

        // Add event on "Activate"/"Deactivate" button
        $('#wdt-activate-plugin-powerful').on('click', function () {
            if (wdt_current_config.wdtActivatedPowerful == 0 || wdt_current_config.wdtActivatedPowerful == '') {
                activatePlugin()
            } else {
                deactivatePlugin()
            }
        });

        // Activate plugin
        function activatePlugin() {
            $('#wdt-activate-plugin-powerful').html('<i class="wpdt-icon-spinner9"></i>Loading...');

            let domain    = location.hostname;
            let subdomain = location.hostname;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpdatatables_activate_plugin',
                    purchaseCodeStore: $('#wdt-purchase-code-store-powerful').val().trim(),
                    wdtNonce: $('#wdtNonce').val(),
                    slug: 'wdt-powerful-filters',
                    domain: domain,
                    subdomain: subdomain
                },
                success: function (response) {
                    let valid = JSON.parse(response).valid;
                    let domainRegistered = JSON.parse(response).domainRegistered;

                    if (valid === true && domainRegistered === true) {
                        wdt_current_config.wdtActivatedPowerful = 1;
                        wdt_current_config.wdtPurchaseCodeStorePowerful = 1;
                        wdtNotify('Success!', 'Plugin has been activated', 'success');
                        $('#wdt-purchase-code-store-powerful').val('');
                        $('.wdt-purchase-code-store-powerful-wrapper').hide();
                        $('.wdt-purchase-code-powerful .wdt-security-massage-wrapper').removeClass('hidden');
                        $('#wdt-activate-plugin-powerful').removeClass('btn-primary').addClass('btn-danger').html('<i class="wpdt-icon-times-circle-full"></i>Deactivate');
                        $('.wdt-envato-activation-powerful').hide()
                    } else if (valid === false) {
                        wdtNotify(wpdatatables_settings_strings.error, wpdatatables_settings_strings.purchaseCodeInvalid, 'danger');
                        $('#wdt-activate-plugin-powerful').html('<i class="wpdt-icon-check-circle-full"></i>Activate');
                    } else {
                        wdtNotify(wpdatatables_settings_strings.error, wpdatatables_settings_strings.activation_domains_limit, 'danger');
                        jQuery('#wdt-activate-plugin-powerful').html('<i class="wpdt-icon-check-circle-full"></i>Activate');
                    }
                },
                error: function () {
                    wdt_current_config.wdtActivatedPowerful = 0;
                    wdtNotify('Error!', 'Unable to activate the plugin. Please try again.', 'danger');
                    $('#wdt-activate-plugin-powerful').html('<i class="wpdt-icon-check-circle-full"></i>Activate');
                }
            });
        }

        // Deactivate plugin
        function deactivatePlugin() {
            $('#wdt-activate-plugin-powerful').html('<i class="wpdt-icon-spinner9"></i>Loading...');
            $('#wdt-envato-deactivation-powerful').html('<i class="wpdt-icon-spinner9"></i>Loading...');

            let domain    = location.hostname;
            let subdomain = location.hostname;
            let params = {
                action: 'wpdatatables_deactivate_plugin',
                wdtNonce: $('#wdtNonce').val(),
                domain: domain,
                subdomain: subdomain,
                slug: 'wdt-powerful-filters',
            };

            if (parseInt(wdt_current_config.wdtPurchaseCodeStorePowerful)) {
                params.type = 'code';
                params.envatoTokenEmail = '';
            } else if (wdt_current_config.wdtEnvatoTokenEmailPowerful) {
                params.type = 'envato';
                params.envatoTokenEmail = wdt_current_config.wdtEnvatoTokenEmailPowerful;
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: params,
                success: function (response) {
                    var parsedResponse = JSON.parse(response);
                    if (parsedResponse.deactivated === true) {
                        wdt_current_config.wdtPurchaseCodeStorePowerful = 0;
                        wdt_current_config.wdtEnvatoTokenEmail = '';
                        wdt_current_config.wdtActivatedPowerful = 0;
                        $('#wdt-purchase-code-store-powerful').val('');
                        $('.wdt-purchase-code-store-powerful-wrapper').show();
                        $('.wdt-purchase-code-powerful .wdt-security-massage-wrapper').addClass('hidden');
                        $('#wdt-activate-plugin-powerful').removeClass('btn-danger').addClass('btn-primary').html('<i class="wpdt-icon-check-circle-full"></i>Activate');
                        $('.wdt-envato-activation-powerful').show()
                        $('.wdt-preload-layer').animateFadeOut();
                        $('#wdt-envato-activation-powerful span').text(wpdatatables_settings_strings.activateWithEnvato);
                        $('#wdt-envato-activation-powerful').prop('disabled', '');
                        $('#wdt-envato-deactivation-powerful').html('<i class="wpdt-icon-times-circle-full"></i>Deactivate').hide()
                        $('.wdt-purchase-code-powerful').show();
                    } else {
                        wdtNotify(wpdatatables_settings_strings.error, wpdatatables_settings_strings.unable_to_deactivate_plugin, 'danger');
                        $('#wdt-activate-plugin-powerful').html('<i class="wpdt-icon-times-circle-full"></i>Deactivate');
                        $('#wdt-envato-deactivation-powerful').html('<i class="wpdt-icon-times-circle-full"></i>Deactivate');
                    }
                }
            });
        }

        function authenticateEnvatoOAuth() {
            let domain    = location.hostname;
            let subdomain = location.hostname;
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpdatatables_parse_server_name',
                    wdtNonce: $('#wdtNonce').val(),
                    domain: domain,
                    subdomain: subdomain
                },
                success: function (response) {
                    let serverName = JSON.parse(response);
                    let domain = serverName.domain;
                    let subdomain = serverName.subdomain;
                    window.location.replace(
                        wdtStore.url + 'activation/envato?slug=wdt-powerful-filters&domain=' + domain + '&subdomain=' + subdomain + '&redirectUrl=' + wdtStore.redirectUrl + '/wp-admin/admin.php?page=wpdatatables-settings'
                    )
                }
            });
        }

        function authenticateEnvatoOAuthCallback() {
            // Get value of valid query parameter
            var valid = searchQueryString('valid');
            var domainRegistered = searchQueryString('domainRegistered');
            var slug = searchQueryString('slug');

            if (valid !== null && slug === 'wdt-powerful-filters') {

                // Remove query parameters sent back from TMS Store
                let redirectURL = this.removeURLParameter(window.location.href, 'valid');
                redirectURL = this.removeURLParameter(redirectURL, 'slug');
                redirectURL = this.removeURLParameter(redirectURL, 'domainRegistered');

                $('.tab-nav a[href="#wdt-activation"]').tab('show');

                if (valid === 'true' && domainRegistered === 'true' && searchQueryString('envatoTokenEmail')) {
                    // Set refresh token
                    wdt_current_config.wdtEnvatoTokenEmailPowerful = searchQueryString('envatoTokenEmail');
                    // Set activated
                    wdt_current_config.wdtActivatedPowerful = 1;

                    // Change button text and disable it
                    $('#wdt-envato-activation-powerful span').text(wpdatatables_settings_strings.envato_api_activated);
                    $('#wdt-envato-activation-powerful').prop('disabled', 'disabled');
                    $('.wdt-purchase-code-powerful').hide();
                    $('#wdt-envato-deactivation-powerful').show();

                    // Save plugin settings
                    $.ajax({
                        url: ajaxurl,
                        dataType: 'json',
                        method: 'POST',
                        data: {
                            action: 'wpdatatables_save_plugin_settings',
                            settings: wdt_current_config
                        },
                        success: function () {
                            $('.wdt-preload-layer').animateFadeOut();
                            wdtNotify(wpdatatables_edit_strings.success, wpdatatables_edit_strings.pluginActivated, 'success');
                        }
                    });

                    redirectURL = this.removeURLParameter(redirectURL, 'envatoTokenEmail')
                } else if (valid === 'false') {
                    wdtNotify(wpdatatables_settings_strings.error, wpdatatables_settings_strings.envato_failed_powerful, 'danger');
                } else if (domainRegistered === 'false') {
                    wdtNotify(wpdatatables_settings_strings.error, wpdatatables_settings_strings.activation_domains_limit, 'danger');
                }

                window.history.pushState(null, null, redirectURL)
            }
        }

        function handleActivationSettings() {
            if (wdt_current_config.wdtActivatedPowerful == 1) {
                if (wdt_current_config.wdtEnvatoTokenEmailPowerful) {
                    // Change button text and disable it
                    $('#wdt-envato-activation-powerful span').text(wpdatatables_settings_strings.envato_api_activated);
                    $('#wdt-envato-activation-powerful').prop('disabled', 'disabled');
                    $('#wdt-envato-deactivation-powerful').show()
                    $('.wdt-purchase-code-powerful').hide()
                } else {
                    $('.wdt-envato-activation-powerful').hide()
                    $('#wdt-purchase-code-store-powerful').val('');
                    $('.wdt-purchase-code-store-powerful-wrapper').hide();
                    $('.wdt-purchase-code-powerful .wdt-security-massage-wrapper').removeClass('hidden');
                    $('#wdt-activate-plugin-powerful').removeClass('btn-primary').addClass('btn-danger').html('<i class="wpdt-icon-times-circle-full"></i>Deactivate');
                }
            } else {
                $('#wdt-purchase-code-store-powerful').val('');
                $('.wdt-purchase-code-store-powerful-wrapper').show();
                $('.wdt-purchase-code-powerful .wdt-security-massage-wrapper').addClass('hidden');
                $('#wdt-activate-plugin-powerful').removeClass('btn-danger').addClass('btn-primary').html('<i class="wpdt-icon-check-circle-full"></i>Activate');
            }
        }
    });
})(jQuery);
