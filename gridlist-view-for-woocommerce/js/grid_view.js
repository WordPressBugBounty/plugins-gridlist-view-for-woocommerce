var lgv_cookie_default = [ 'default', 'default'];
window.addEventListener('popstate', br_lgv_before_block_navigation, true);
(function ($){
    $(document).ready( function () {
        setTimeout(br_lgv_update_state, 0);
        $(document).on('wc-blocks_product_list_rendered berocket_ajax_products_loaded berocket_ajax_products_infinite_loaded', function() {
            queueMicrotask(br_lgv_update_state);
        });
        var navigation = 0;
        document.addEventListener('click', async function(event) {
            if( event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey ) return;
            var link = event.target.closest('.wp-block-woocommerce-product-collection .wp-block-query-pagination a');
            if( ! link || ! link.hasAttribute('data-wp-on--click') || link.target === '_blank'
                || ! jQuery(link).closest('.wp-block-woocommerce-product-collection').find('.berocket_lgv_list_container').length ) return;
            event.preventDefault();
            var current = ++navigation;
            try {
                var router = await import('@wordpress/interactivity-router');
                await router.actions.prefetch(link.href);
                if( current !== navigation ) return;
                br_lgv_before_block_navigation();
                await router.actions.navigate(link.href);
                br_lgv_update_state();
                document.querySelector('.wp-block-woocommerce-product-collection .wc-block-product a')?.focus();
                var context = JSON.parse(jQuery(link).closest('.wp-block-woocommerce-product-collection').attr('data-wp-context') || '{}');
                document.body.dispatchEvent(new CustomEvent('wc-blocks_product_list_rendered', { bubbles: true, detail: { collection: context.collection } }));
            } catch(error) {
                if( current === navigation ) location.assign(link.href);
            }
        }, true);
        var list_style_page = set_get_lgv_cookie ( 0 );
        $('.berocket_lgv_widget a.berocket_lgv_set').removeClass('selected');
        $('.berocket_lgv_button_'+list_style_page).addClass('selected');
        $(document).on( 'click', '.berocket_lgv_widget a.berocket_lgv_set', function ( event ) {
            event.preventDefault();
            set_get_lgv_cookie ( 0, $(this).data('type') );
            if( lgv_options.user_func != null )
                lgv_execute_func ( lgv_options.user_func.before_buttons_reselect );
            jQuery(document).trigger('berocket_lgv_before_buttons_reselect');
            $('.berocket_lgv_widget a.berocket_lgv_set').removeClass('selected');
            $('.berocket_lgv_button_'+$(this).data('type')).addClass('selected');
            if( lgv_options.user_func != null )
                lgv_execute_func ( lgv_options.user_func.after_buttons_reselect );
            jQuery(document).trigger('berocket_lgv_after_buttons_reselect');
            br_lgv_style_set();
        });
        $(document).on( 'click', 'a.br_lgv_product_count_set', function ( event ) {
            event.preventDefault();
            event.stopPropagation();
            if ( $(this).hasClass( 'selected' ) ) {
                return false;
            } else {
                if( lgv_options.user_func != null )
                    lgv_execute_func ( lgv_options.user_func.before_product_reselect );
                jQuery(document).trigger('berocket_lgv_before_product_reselect');
                $('a.br_lgv_product_count_set').removeClass( 'selected' );
                $('a.br_lgv_product_count_set.value_'+$(this).data('type')).addClass( 'selected' );
                if( lgv_options.user_func != null )
                    lgv_execute_func ( lgv_options.user_func.after_product_reselect );
                jQuery(document).trigger('berocket_lgv_after_product_reselect');
            }
            set_get_lgv_cookie ( 1, $(this).data('type') );
            if ( typeof updateProducts === "function" ) {
                if( lgv_options.user_func != null )
                    lgv_execute_func ( lgv_options.user_func.before_ajax_product_reload );
                jQuery(document).trigger('berocket_lgv_before_ajax_product_reload');
                first_page = the_ajax_script.first_page;
                the_ajax_script.first_page = true;
                updateProducts( true );
                the_ajax_script.first_page = first_page;
                if( lgv_options.user_func != null )
                    lgv_execute_func ( lgv_options.user_func.after_ajax_product_reload );
                jQuery(document).trigger('berocket_lgv_after_ajax_product_reload');
            } else {
                if( lgv_options.user_func != null )
                    lgv_execute_func ( lgv_options.user_func.before_page_reload );
                jQuery(document).trigger('berocket_lgv_before_page_reload');
                var url = new URL(location.href);
                url.hash = '';
                Array.from(url.searchParams.keys()).forEach(function(key) {
                    if( key === 'paged' || /^query-(?:\d+-)?page$/.test(key) ) url.searchParams.delete(key);
                });
                url.pathname = url.pathname.replace(/\/page\/\d+\/?/, '/');
                var new_location = url.href;
                if(location == new_location) {
                    location.reload();
                } else {
                    location = new_location;
                }
            }
        });
    });
})(jQuery);
function br_lgv_before_block_navigation(event) {
    if( jQuery('.wp-block-woocommerce-product-collection .berocket_lgv_list_container').length ) {
        br_lgv_style_set('grid');
        if( event && event.type === 'popstate' ) setTimeout(br_lgv_update_state, 0);
    }
}
function br_lgv_update_state() {
    br_lgv_style_set();
    var style = set_get_lgv_cookie(0), count = set_get_lgv_cookie(1);
    jQuery('.berocket_lgv_set, a.br_lgv_product_count_set').each(function() {
        var button = jQuery(this);
        button.toggleClass('selected', button.attr('data-type') == (button.is('.berocket_lgv_set') ? style : count));
    });
}
function br_lgv_style_set(style) {
    br_lgv_stat_cookie = typeof style === 'string' ? style : set_get_lgv_cookie ( 0 );
    if( br_lgv_stat_cookie ) {
        if( lgv_options.user_func != null )
            lgv_execute_func ( lgv_options.user_func.before_style_set );
        jQuery(document).trigger('berocket_lgv_before_style_set');
        if( br_lgv_stat_cookie == 'list' ) {
            // Only override grid containers whose product descriptions are moved out below.
            jQuery( '.berocket_lgv_list_grid .berocket_lgv_additional_data' ).closest( '.berocket_lgv_list_grid' ).parent().each( function() {
                var display = jQuery(this).css( 'display' );
                if( display == 'grid' || display == 'inline-grid' ) {
                    jQuery(this).addClass( 'berocket_lgv_list_container' );
                }
            });
            jQuery( '.berocket_lgv_list_grid' ).removeClass( 'berocket_lgv_grid' ).addClass( 'berocket_lgv_list' );
            jQuery( '.berocket_lgv_list_grid .berocket_lgv_additional_data' ).each( function( i, o ) {
                jQuery(o).parents( '.berocket_lgv_list_grid' ).css( 'float', 'left' ).after(jQuery(o));
                jQuery(o).after( jQuery( '<div class="berocket_lgv_after_additional"></div>' ) );
            });
            if( lgv_options.user_func != null )
                lgv_execute_func ( lgv_options.user_func.after_style_list );
            jQuery(document).trigger('berocket_lgv_after_style_list');
        } else {
            jQuery( '.berocket_lgv_list_container' ).removeClass( 'berocket_lgv_list_container' );
            jQuery( '.berocket_lgv_after_additional' ).remove();
            jQuery( '.berocket_lgv_list_grid' ).removeClass( 'berocket_lgv_list' ).addClass( 'berocket_lgv_grid' );
            jQuery( '.berocket_lgv_additional_data' ).each( function( i, o ) {
                if( jQuery(o).prev().hasClass( 'berocket_lgv_list_grid' ) ) {
                    jQuery(o).prev().css( 'float', '' ).first().append( jQuery(o) );
                } else {
                    jQuery(o).prev().find( '.berocket_lgv_list_grid' ).css( 'float', '' ).first().append( jQuery(o) );
                }
            });
            if( lgv_options.user_func != null )
                lgv_execute_func ( lgv_options.user_func.after_style_grid );
            jQuery(document).trigger('berocket_lgv_after_style_grid');
        }
        if( lgv_options.user_func != null )
            lgv_execute_func ( lgv_options.user_func.after_style_set );
        jQuery(document).trigger('berocket_lgv_after_style_set');
    }
}
function set_get_lgv_cookie( position, value ){
    if ( typeof value === "undefined" ) {
        value = false;
    }
    if( lgv_options.user_func != null )
        lgv_execute_func ( lgv_options.user_func.before_get_cookie );
    jQuery(document).trigger('berocket_lgv_before_get_cookie');
    if( position == 0 
        && typeof(lgv_options.style_on_pages) != 'undefined'
        && typeof(lgv_options.style_on_pages.pages) != 'undefined'
        && lgv_options.style_on_pages.pages != false 
    ) {
        br_lgv_stat_cookie = lgv_options.style_on_pages.pages;
        jQuery('.berocket_lgv_set, .br-lgv-switcher').remove();
    } else if( position == 0 
        && jQuery(window).width() <= lgv_options.max_mobile_width
        && typeof(lgv_options.style_on_pages) != 'undefined'
        && typeof(lgv_options.style_on_pages.mobile_pages) != 'undefined'
        && lgv_options.style_on_pages.mobile_pages != false 
    ) {
        br_lgv_stat_cookie = lgv_options.style_on_pages.mobile_pages;
        jQuery('.berocket_lgv_set, .br-lgv-switcher').remove();
    } else if( position == 0 
        && jQuery(window).width() > lgv_options.max_mobile_width
        && typeof(lgv_options.style_on_pages) != 'undefined'
        && typeof(lgv_options.style_on_pages.desktop_pages) != 'undefined'
        && lgv_options.style_on_pages.desktop_pages != false 
    ) {
        br_lgv_stat_cookie = lgv_options.style_on_pages.desktop_pages;
        jQuery('.berocket_lgv_set, .br-lgv-switcher').remove();
    } else {
        br_lgv_stat_cookie = jQuery.cookie( 'br_lgv_stat' );
        if ( jQuery.cookie( 'br_lgv_stat' ) && br_lgv_stat_cookie.indexOf('|') > 0 && ( br_lgv_stat_cookie.split( '|' ).length - 1 ) >= position && value == false ) {
            br_lgv_stat_cookie = br_lgv_stat_cookie.split( '|' )[position];
        } else {
            br_lgv_stat_cookie = false;
        }
        if( value !== false ) {
            br_lgv_stat_cookie = lgv_cookie_default;
            if ( jQuery.cookie( 'br_lgv_stat' ) ) {
                br_lgv_stat = jQuery.cookie( 'br_lgv_stat' );
                br_lgv_stat = br_lgv_stat.split( '|' );
                for ( var i = 0; i < br_lgv_stat.length; i++ ) {
                    br_lgv_stat_cookie[i] = br_lgv_stat[i];
                }
            }
            br_lgv_stat_cookie[position] = value;
            br_lgv_stat_cookie = br_lgv_stat_cookie.join('|');
            jQuery.cookie( 'br_lgv_stat', br_lgv_stat_cookie, { path: '/', domain: document.domain } );
        } else if( position == 0 && br_lgv_stat_cookie == 'default' ) {
            if( jQuery(window).width() <= lgv_options.max_mobile_width ) {
                br_lgv_stat_cookie = lgv_options.mobile_default_style;
            } else {
                br_lgv_stat_cookie = lgv_options.default_style;
            }
        }
    }
    if( lgv_options.user_func != null )
        lgv_execute_func ( lgv_options.user_func.after_get_cookie );
    jQuery(document).trigger('berocket_lgv_after_get_cookie');
    return br_lgv_stat_cookie;
}
function lgv_execute_func ( func ) {
    if( lgv_options.user_func != 'undefined'
        && lgv_options.user_func != null
        && typeof func != 'undefined' 
        && func.length > 0 ) {
        try{
            eval( func );
        } catch(err){
            alert('You have some incorrect JavaScript code (Grid/List View)');
        }
    }
}
