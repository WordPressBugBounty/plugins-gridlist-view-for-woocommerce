<?php 
global $wp_query;
$BeRocket_LGV = BeRocket_LGV::getInstance();
$options = $BeRocket_LGV->get_option();
$options = $options['liststyle']; 
$link_class = BeRocket_LGV::sanitize_html_classes( ! empty( $options['button']['lgv_link_simple']['custom_class'] ) ? $options['button']['lgv_link_simple']['custom_class'] : 'lgv_link lgv_link_simple', 'lgv_link lgv_link_simple' );
$description_class = BeRocket_LGV::sanitize_html_classes( ! empty( $options['button']['lgv_description_simple']['custom_class'] ) ? $options['button']['lgv_description_simple']['custom_class'] : 'lgv_description lgv_description_simple', 'lgv_description lgv_description_simple' );
$meta_class = BeRocket_LGV::sanitize_html_classes( ! empty( $options['button']['lgv_meta_simple']['custom_class'] ) ? $options['button']['lgv_meta_simple']['custom_class'] : 'lgv_meta lgv_meta_simple', 'lgv_meta lgv_meta_simple' );
$price_class = BeRocket_LGV::sanitize_html_classes( ! empty( $options['button']['lgv_price_simple']['custom_class'] ) ? $options['button']['lgv_price_simple']['custom_class'] : 'lgv_price lgv_price_simple', 'lgv_price lgv_price_simple' );
?>
<div class="berocket_lgv_additional_data">
    <?php
    do_action( 'lgv_simple_before' );
    ?>
    <a class="<?php echo esc_attr( $link_class ); ?>" href="<?php the_permalink(); ?>">
        <h3><?php the_title(); ?></h3>
    </a>
    <?php
    do_action( 'lgv_simple_after_product_name' );
    ?>
    <div class="<?php echo esc_attr( $description_class ); ?>">
        <?php
        woocommerce_template_single_excerpt();
        ?>
    </div>
    <?php
    do_action( 'lgv_simple_after_description' );
    ?>
    <div class="<?php echo esc_attr( $meta_class ); ?>">
        <?php
        woocommerce_template_single_meta();
        ?>
    </div>
    <?php
    do_action( 'lgv_simple_after_meta' );
    ?>
    <div class="<?php echo esc_attr( $price_class ); ?>">
        <?php
        woocommerce_template_loop_price();
        ?>
    </div>
    <?php
    do_action( 'lgv_simple_after' );
    ?>
    <script>
        if( typeof(br_lgv_style_set) == 'function' ) {
            br_lgv_style_set();
        } else {
            jQuery(document).ready( function () {
                if( typeof(br_lgv_style_set) == 'function' ) {
                    br_lgv_style_set();
                }
            });
        }
    </script>
</div>
