;(function($){
"use strict";

    var $body = $('body'),
        $popup = $('.htcompare-popup'),
        cachedNonce = null,
        noncePromise = null;

    // Cookie helper: read compare list from cookie
    function getCompareListFromCookie() {
        var cookieName = evercompare.cookie_name;
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();
            if (cookie.indexOf(cookieName + '=') === 0) {
                var value = decodeURIComponent(cookie.substring(cookieName.length + 1));
                try {
                    var parsed = JSON.parse(value);
                    if (Array.isArray(parsed)) {
                        return parsed.map(function(id) { return parseInt(id, 10); }).filter(function(id) { return id > 0; });
                    }
                } catch(e) {}
            }
        }
        return [];
    }

    // Deferred nonce: fetch on first interaction, cache in memory
    function getNonce() {
        if (cachedNonce) {
            return $.Deferred().resolve(cachedNonce).promise();
        }
        if (noncePromise) {
            return noncePromise;
        }
        noncePromise = $.ajax({
            url: evercompare.ajaxurl,
            data: { action: 'ever_compare_get_nonce' },
            dataType: 'json',
            method: 'GET',
        }).then(function(response) {
            cachedNonce = response.nonce;
            noncePromise = null;
            return cachedNonce;
        }).fail(function() {
            noncePromise = null;
        });
        return noncePromise;
    }

    // Sync button states from cookie on DOM ready
    function syncButtonStates() {
        var compareList = getCompareListFromCookie();
        $('a.htcompare-btn').each(function() {
            var $btn = $(this);
            var productId = parseInt($btn.data('product_id'), 10);
            if (compareList.indexOf(productId) >= 0) {
                $btn.addClass('added');
                $btn.html('<span class="htcompare-btn-text">' + $btn.data('added-text') + '</span>');
            }
        });
    }

    // Sync counter from cookie on DOM ready
    function syncCounter() {
        var compareList = getCompareListFromCookie();
        $body.find('.htcompare-counter').html(compareList.length);
    }

    // Load compare table via AJAX for cookie-based pages
    function loadCompareTable() {
        var $tables = $('.htcompare-table[data-ajax-load]');
        if ($tables.length === 0) return;

        var compareList = getCompareListFromCookie();
        if (compareList.length === 0) {
            // Show empty state — fetch table with no IDs to get proper empty template
            $.ajax({
                url: evercompare.ajaxurl,
                data: {
                    action: 'ever_compare_get_table',
                    ids: '',
                },
                dataType: 'json',
                method: 'GET',
                success: function(response) {
                    if (typeof response.table !== 'undefined') {
                        $tables.each(function() {
                            $(this).replaceWith(response.table || '<div class="htcompare-table htcompare-empty"></div>');
                        });
                        bindShareableLinkHandler();
                    }
                }
            });
            return;
        }

        $.ajax({
            url: evercompare.ajaxurl,
            data: {
                action: 'ever_compare_get_table',
                ids: compareList.join(','),
            },
            dataType: 'json',
            method: 'GET',
            success: function(response) {
                if (response.table) {
                    $tables.each(function() {
                        $(this).replaceWith(response.table);
                    });
                    bindShareableLinkHandler();
                }
            }
        });
    }

    function bindShareableLinkHandler() {
        $('.evercompare-copy-link').on('click', function(e) {
            evercompareCopyToClipboard($(this).closest('.ever-compare-shareable-link').find('.evercompare-share-link'), this);
        });
    }

    // Error modal
    $body.append('<div id="htcompare-error-modal" class="htcompare-error-modal" style="display:none"><div class="htcompare-error-modal-content"><button class="htcompare-error-modal-close">&times;</button><div class="htcompare-error-modal-body"></div></div></div>');
    var $htcompareErrorModal = $("#htcompare-error-modal");
    $('.htcompare-error-modal-close').on('click', function() {
        $htcompareErrorModal.css('display', 'none');
    });
    $(window).on('click', function(e) {
        if(e.target == $htcompareErrorModal[0]) {
            $htcompareErrorModal.css('display', 'none');
        }
    });

    // Sync states on DOM ready
    syncButtonStates();
    syncCounter();
    loadCompareTable();

    // Notification Markup
    var notificationMarkup = '<div class="htcompare-notification" aria-live="polite"><div class="htcompare-notification-text">' + evercompare.option_data.success_added_notification_text + '</div><span class="htcompare-notification-close">close</span></div>';
    if(evercompare.option_data.enable_success_notification === 'on') {
        $body.append(notificationMarkup);
        $body.on('click', '.htcompare-notification-close', function() {
            $body.find('.htcompare-notification').removeClass('open');
        });
    }
    // Notification Show Function
    var ShowNotification = function(message) {
        if(evercompare.option_data.enable_success_notification === 'on') {
            $body.find('.htcompare-notification-text').html(message);
            $body.find('.htcompare-notification').addClass('open');
            if(+evercompare.option_data.removed_notification_after > -1) {
                setTimeout(function() {
                    $body.find('.htcompare-notification').removeClass('open');
                }, +evercompare.option_data.removed_notification_after * 1000);
            }
        }
    };

    // Add product in compare table
    $body.on('click', 'a.htcompare-btn', function (e) {
        var $this = $(this),
            id = $this.data('product_id'),
            addedText = $this.data('added-text'),
            success_message = evercompare.option_data.success_added_notification_text.replace('{product_name}', $this.data('product_title'));

        if( evercompare.popup === 'yes' &&  evercompare.option_data.remove_on_click === 'off' ){
            e.preventDefault();
            if ($this.hasClass('added') ) {
                $body.find('.htcompare-popup').addClass('open');
                return true;
            }
        }else{
            if ( $this.hasClass('added') ) return true;
        }

        e.preventDefault();

        $this.addClass('loading');

        getNonce().then(function(nonce) {
            $.ajax({
                url: evercompare.ajaxurl,
                data: {
                    action: 'ever_compare_add_to_compare',
                    nonce: nonce,
                    id: id,
                },
                dataType: 'json',
                method: 'GET',
                success: function ( response ) {
                    if ( !response || !response.products ) {
                        $body.find('.htcompare-counter').html( response ? response.count : 0 );
                        return;
                    }
                    var $products = typeof response.products === 'object' ? Object.values(response.products) : response.products;
                    var productStrings = $products.map(function(p) { return p.toString(); });
                    if ( response.table && productStrings.indexOf(id.toString()) >= 0 ) {
                        updateCompareData( response );
                        $popup.addClass('open');
                    } else {
                        $('.htcompare-error-modal-body').html(response.limitReached);
                        $htcompareErrorModal.css('display', 'flex');
                    }
                    $body.find('.htcompare-counter').html( response.count );
                },
                error: function ( data ) {
                    console.log('Something wrong with AJAX response.');
                },
                complete: function (res) {
                    $this.removeClass('loading');
                    if (res.responseJSON && res.responseJSON.products) {
                        var $products = typeof res.responseJSON.products === 'object' ? Object.values(res.responseJSON.products) : res.responseJSON.products;
                        var productStrings = $products.map(function(p) { return p.toString(); });
                        if(productStrings.indexOf(id.toString()) >= 0) {
                            $this.addClass('added');
                            $this.html('<span class="htcompare-btn-text">'+addedText+'</span>');
                            ShowNotification(success_message);
                        }
                    }
                },
            });
        }).fail(function() {
            $this.removeClass('loading');
        });

    });

    if(evercompare.option_data.remove_on_click && evercompare.option_data.remove_on_click === 'on') {
        $body.on('click', 'a.htcompare-btn.added', function (e) {
            e.preventDefault();
            var $this = $(this),
                id = $this.data('product_id'),
                success_message = evercompare.option_data.success_removed_notification_text.replace('{product_name}', $this.data('product_title'));
            $this.addClass('loading');
            getNonce().then(function(nonce) {
                jQuery.ajax({
                    url: evercompare.ajaxurl,
                    data: {
                        action: 'ever_compare_remove_from_compare',
                        nonce: nonce,
                        id: id,
                    },
                    dataType: 'json',
                    method: 'GET',
                    success: function (response) {
                        if (response) {
                            $body.find('.htcompare-counter').html( response.count );
                        } else {
                            console.log( 'Something wrong loading compare data' );
                        }
                    },
                    error: function (data) {
                        console.log('Something wrong with AJAX response.');
                    },
                    complete: function (res) {
                        $this.removeClass('loading added').html('<span class="htcompare-btn-text">'+$this.data('text')+'</span>');
                        if (res.responseJSON && res.responseJSON.count !== undefined) {
                            ShowNotification(success_message);
                        }
                    },
                });
            }).fail(function() {
                $this.removeClass('loading');
            });
        });
    }

    // Remove data from compare table
    $body.on('click', 'a.htcompare-remove', function (e) {
        var $table = $('.htcompare-table');

        e.preventDefault();
        var $this = $(this),
            id = $this.data('product_id'),
            success_message = evercompare.option_data.success_removed_notification_text.replace('{product_name}', $this.data('product_title'));

        $table.addClass('loading');
        $this.addClass('loading');

        getNonce().then(function(nonce) {
            jQuery.ajax({
                url: evercompare.ajaxurl,
                data: {
                    action: 'ever_compare_remove_from_compare',
                    nonce: nonce,
                    id: id,
                },
                dataType: 'json',
                method: 'GET',
                success: function (response) {
                    if (response.table) {
                        updateCompareData(response);
                    } else {
                        console.log( 'Something wrong loading compare data' );
                    }
                    $('a.htcompare-btn').each(function() {
                        if($(this).data('product_id') === id) {
                            $(this).removeClass('added');
                            $(this).html('<span class="htcompare-btn-text">'+$(this).data('text')+'</span>');
                        }
                    });
                    $body.find('.htcompare-counter').html( response.count );
                },
                error: function (data) {
                    console.log('Something wrong with AJAX response.');
                },
                complete: function (res) {
                    $table.removeClass('loading');
                    $this.removeClass('loading');
                    if (res.responseJSON && res.responseJSON.table) {
                        ShowNotification(success_message);
                    }
                },
            });
        }).fail(function() {
            $table.removeClass('loading');
            $this.removeClass('loading');
        });

    });

    // Update table HTML
    function updateCompareData( data ) {
        if ( $('.htcompare-table').length > 0 ) {
            $('.htcompare-table').replaceWith( data.table );
            bindShareableLinkHandler();
        }
    }

    // Close popup
    $body.on('click','.htcompare-popup-close', function(e){
        $(this).parent('.htcompare-popup.open').removeClass('open');
        $popup.removeClass('open');
    });

    // Copy Shareable link
    bindShareableLinkHandler();
    function evercompareCopyToClipboard( element, button ) {
        var $tempdata = $("<input>");
        $("body").append($tempdata);
        $tempdata.val($(element).text()).select();
        document.execCommand("copy");
        $tempdata.remove();
        $(button).text( $(button).data('copytext') );
        setTimeout(function() {
            $( button ).text( $(button).data('btntext') );
        }, 1000);
    }

})(jQuery);
