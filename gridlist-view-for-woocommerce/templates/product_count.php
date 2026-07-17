<?php  
if( ! empty($options['value']) ) {
    $values = $options['value'];
} elseif( ! empty($test_values) ) {
    $values = $test_values;
}
$br_lgv_stat_products = '';
if ( ! empty($options['use']) ) {
    $product_count_per_page = br_lgv_get_cookie ( 1 );
    if( (int)$product_count_per_page ) {
        $br_lgv_stat_products = (int)$product_count_per_page;
    } elseif ( $product_count_per_page == 'all' ) {
        $br_lgv_stat_products = 'all';
    }
}
$exploder = (empty($options['explode']) ? '' : $options['explode']);
$exploder = preg_replace( '/\s+/','', $exploder );
if( ! empty($values) ) {
    $position = BeRocket_LGV::sanitize_position( isset( $position ) ? $position : '' );
    $custom_class = BeRocket_LGV::sanitize_html_classes( ( @ $custom_class ) ? $custom_class : 'br_lgv_product_count', 'br_lgv_product_count' );
    ?><div class="br_lgv_product_count_block" style="<?php echo esc_attr( ( $position ? 'float:' . $position . ';' : '' ) ); ?>"><?php
    do_action( 'lgv_before_product_count_links' );
    $values = strtolower( @ $values );
    $values = preg_replace( '/\s+/', '', $values );
    $values = apply_filters( 'lgv_product_count_values', $values );
    $values = explode( ',', @ $values );
    echo '<span class="br_lgv_product_count text">' . wp_kses_post( @ $options['text_before'] ) . '</span>';
    $first = true;
    foreach( $values as $value ) {
        if( ! $first ) {
            ?><span class="br_lgv_product_count"><?php echo esc_html( $exploder ); ?></span><?php
        } else {
            $first = false;
        }
        if( $value == 'all' ) {
            ?><a href="#" data-type="<?php echo esc_attr( $value ); ?>" class="<?php echo esc_attr( 'br_lgv_product_count_set ' . $custom_class . ' value_' . $value . ( $value == $br_lgv_stat_products ? ' selected' : '' ) ); ?>"><?php _e( 'All', 'BeRocket_LGV_domain' ) ?></a><?php
        } elseif( ( (int)$value ) > 0 ) {
            ?><a href="#" data-type="<?php echo esc_attr( (int) $value ); ?>" class="<?php echo esc_attr( 'br_lgv_product_count_set ' . $custom_class . ' value_' . (int) $value . ( $value == $br_lgv_stat_products ? ' selected' : '' ) ); ?>"><?php echo (int)$value ?></a><?php
        }
    }
    echo '<span class="br_lgv_product_count text">' . wp_kses_post( @ $options['text_after'] ) . '</span>';
    do_action( 'lgv_after_product_count_links' );
    ?></div><?php
}
?>
