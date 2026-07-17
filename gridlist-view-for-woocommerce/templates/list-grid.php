<?php
$position = BeRocket_LGV::sanitize_position( isset( $position ) ? $position : '' );
$padding = BeRocket_LGV::sanitize_padding( isset( $padding ) ? $padding : array() );
$button_class = BeRocket_LGV::sanitize_html_classes( empty( $custom_class ) ? 'berocket_lgv_button' : $custom_class, 'berocket_lgv_button' );
$style = ( empty( $position ) ? '' : 'float:' . $position . ';' ) . 'padding: ' . $padding['top'] . 'px ' . $padding['right'] . 'px ' . $padding['bottom'] . 'px ' . $padding['left'] . 'px;';
if ( ! empty($title) ) {
    ?><div class="berocket_lgv_title"><?php
    if( ! empty($args['before_title']) ) {
        echo $args['before_title'];
    }
    echo wp_kses_post( $title );
    if( ! empty($args['after_title']) ) {
        echo $args['after_title'];
    }
    ?></div><?php
}
$product_style = br_lgv_get_cookie ( 0 );
?>
<div class="berocket_lgv_widget" style="<?php echo esc_attr( $style ); ?>">
    <?php do_action('br_lgv_before_list_grid_buttons');
    global $berocket_hide_grid_list_buttons;
    if( !$berocket_hide_grid_list_buttons ) { ?>
    <a href="#" data-type="grid" class="<?php echo esc_attr( 'berocket_lgv_set ' . $button_class . ( ( $product_style == 'grid' || ! $product_style ) ? ' selected' : '' ) . ' berocket_lgv_button_grid' ); ?>"><i class="fa fa-th"></i></a>
    <a href="#" data-type="list" class="<?php echo esc_attr( 'berocket_lgv_set ' . $button_class . ( $product_style == 'list' ? ' selected' : '' ) . ' berocket_lgv_button_list' ); ?>"><i class="fa fa-bars"></i></a>
    <?php }
    do_action('br_lgv_after_list_grid_buttons') ?>
</div>
