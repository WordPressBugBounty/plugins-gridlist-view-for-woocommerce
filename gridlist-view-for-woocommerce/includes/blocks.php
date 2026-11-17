<?php
if ( ! defined('ABSPATH') ) {
    exit;
}

class BeRocket_LGV_Blocks {
    public static function settings($data) {
        foreach ( array('Buttons' => 'buttons_page', 'Product Count' => 'product_count') as $tab => $group ) {
            foreach ( array('before_collection' => __('Before Product Collection', 'BeRocket_LGV_domain'),
                'after_collection' => __('After Product Collection', 'BeRocket_LGV_domain')) as $position => $label ) {
                $data[$tab]['buttons_display']['items'][] = array(
                    'label_for' => $label, 'type' => 'checkbox', 'name' => array($group, $position), 'value' => '1',
                );
            }
        }
        return $data;
    }

    public static function mark_product_template($block, $source, $parent = null) {
        if ( $block['blockName'] === 'woocommerce/product-template' ) {
            $block['attrs']['br_lgv_product_template'] = $parent && ! empty($parent->attributes['query']['inherit']);
        }
        return $block;
    }

    public static function render_product($content, $block) {
        if ( ! empty($block['attrs']['br_lgv_product_template']) ) {
            if ( strpos($content, 'berocket_lgv_additional_data') === false ) {
                ob_start();
                BeRocket_LGV::getInstance()->additional_product_data();
                $content .= ob_get_clean();
            }
            // Initialize after WooCommerce finishes rendering, not while it inserts each card.
            $content = preg_replace('~<script>\s*if\(\s*typeof\(br_lgv_style_set\).*?</script>~s', '', $content);
        }
        return $content;
    }

    public static function render_collection($content, $block) {
        if ( empty($block['attrs']['query']['inherit']) ) {
            return $content;
        }
        $lgv = BeRocket_LGV::getInstance();
        $options = $lgv->get_option();
        foreach ( array('before_collection', 'after_collection') as $position ) {
            ob_start();
            if ( ! empty($options['buttons_page'][$position]) ) {
                $lgv->show_buttons_fix();
            }
            if ( ! empty($options['product_count'][$position]) ) {
                $lgv->show_product_count();
            }
            $controls = ob_get_clean();
            $content = $position === 'before_collection' ? $controls . $content : $content . $controls;
        }
        return $content;
    }
}

add_filter('brfr_data_list_grid', array('BeRocket_LGV_Blocks', 'settings'));
add_filter('render_block_data', array('BeRocket_LGV_Blocks', 'mark_product_template'), 10, 3);
add_filter('render_block_core/null', array('BeRocket_LGV_Blocks', 'render_product'), 10, 2);
add_filter('render_block_woocommerce/product-collection', array('BeRocket_LGV_Blocks', 'render_collection'), 10, 2);
