jQuery(function (r) {

    // prepare tabs
    r('#wc-rent-payment #tabs').tabs();

    // charge with token ajax
    r('#wc-rent-payment #charge-token-btn').click(function (e) {
        r('#wc-rent-payment #charge-notice').hide();
        r('#wc-rent-payment #charge-token-spinner').show();
        var data = {
            action : r('#wc-rent-payment input[name=action]').val(),
            _wpnonce: r('#wc-rent-payment input[name=_wpnonce]').val(),
            _wp_http_referer: r('#wc-rent-payment input[name=_wp_http_referer]').val(),
            order_id: r('#wc-rent-payment input[name=order_id]').val(),
            token: r('#wc-rent-payment #tab-token input[name=token]').val(),
            amount: r('#wc-rent-payment #tab-token input[name=charge-amount]').val(),
        };
        r.post(ajaxurl, data, function (response) {
            if (!response.success) {
                r('#wc-rent-payment #charge-notice')
                    .show()
                    .addClass('notice-error')
                    .html(response.data.message);
            } else {
                r('#wc-rent-payment #charge-notice')
                    .show()
                    .removeClass('notice-error')
                    .addClass('notice-success')
                    .html(response.data.message);
            }
            r('#wc-rent-payment #charge-token-spinner').hide();
        });
    });

    // charge with cc ajax
    r('#wc-rent-payment #charge-cc-btn').click(function (e) {
        r('#wc-rent-payment #charge-notice').hide();
        r('#wc-rent-payment #charge-cc-spinner').show();
        var data = {
            action: r('#wc-rent-payment input[name=action]').val(),
            _wpnonce: r('#wc-rent-payment input[name=_wpnonce]').val(),
            _wp_http_referer: r('#wc-rent-payment input[name=_wp_http_referer]').val(),
            order_id: r('#wc-rent-payment input[name=order_id]').val(),
            number: r('#wc-rent-payment input#cc-num').val(),
            type: r('#wc-rent-payment select#cc-type').val(),
            expiration: r('#wc-rent-payment input#cc-exp').val(),
            cvc: r('#wc-rent-payment input#cc-cvc').val(),
            amount: r('#wc-rent-payment #tab-cc input[name=charge-amount]').val(),
        };
        r.post(ajaxurl, data, function (response) {
            if (!response.success) {
                r('#wc-rent-payment #charge-notice')
                    .show()
                    .addClass('notice-error')
                    .html(response.data.message);
            } else {
                r('#wc-rent-payment #charge-notice')
                    .show()
                    .removeClass('notice-error')
                    .addClass('notice-success')
                    .html(response.data.message);
            }
            r('#wc-rent-payment #charge-cc-spinner').hide();
        });
    });

});
