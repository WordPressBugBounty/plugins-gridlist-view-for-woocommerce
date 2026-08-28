<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BRGL_Divi5_Module_Renderer {
    protected $defaults = array(
        'title'    => '',
        'all_page' => 'on',
        'position' => '',
    );

    public function render_module( $attrs ) {
        $atts = $this->attrs_to_atts( $attrs );
        $atts = BeRocket_LGV::sanitize_widget_instance(
            array(
                'title'    => $atts['title'],
                'all_page' => $this->is_on( $atts['all_page'] ) ? 1 : 0,
                'position' => $atts['position'],
                'padding'  => array(
                    'top'    => 0,
                    'right'  => 0,
                    'bottom' => 0,
                    'left'   => 0,
                ),
            )
        );

        ob_start();
        the_widget( 'berocket_lgv_widget', $atts );
        return ob_get_clean();
    }

    public function attrs_to_atts( $attrs ) {
        $atts = $this->defaults;

        foreach( array_keys( $this->defaults ) as $key ) {
            $value = $this->get_attr_value( $attrs, $key );
            if( null !== $value ) {
                $atts[$key] = $value;
            }
        }

        return $atts;
    }

    protected function get_attr_value( $attrs, $key ) {
        if( isset( $attrs[$key]['innerContent']['desktop']['value'] ) ) {
            return $this->normalize_attr_value( $attrs[$key]['innerContent']['desktop']['value'] );
        }

        if( isset( $attrs[$key] ) && ! is_array( $attrs[$key] ) ) {
            return $attrs[$key];
        }

        return null;
    }

    protected function normalize_attr_value( $value ) {
        if( is_array( $value ) ) {
            foreach( array( 'value', 'number', 'amount' ) as $value_key ) {
                if( isset( $value[$value_key] ) && '' !== $value[$value_key] && ! is_array( $value[$value_key] ) ) {
                    return $value[$value_key];
                }
            }
            return '';
        }

        return $value;
    }

    protected function is_on( $value ) {
        return true === $value || 1 === $value || '1' === $value || 'on' === $value;
    }
}
