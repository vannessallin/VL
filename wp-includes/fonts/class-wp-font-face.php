<?php
 class WP_Font_Face { private $font_face_property_defaults = array( 'font-family' => '', 'font-style' => 'normal', 'font-weight' => '400', 'font-display' => 'fallback', ); private $valid_font_face_properties = array( 'ascent-override', 'descent-override', 'font-display', 'font-family', 'font-stretch', 'font-style', 'font-weight', 'font-variant', 'font-feature-settings', 'font-variation-settings', 'line-gap-override', 'size-adjust', 'src', 'unicode-range', ); private $valid_font_display = array( 'auto', 'block', 'fallback', 'swap', 'optional' ); private $style_tag_attrs = array(); public function __construct() { if ( function_exists( 'is_admin' ) && ! is_admin() && function_exists( 'current_theme_supports' ) && ! current_theme_supports( 'html5', 'style' ) ) { $this->style_tag_attrs = array( 'type' => 'text/css' ); } } public function generate_and_print( array $fonts ) { $fonts = $this->validate_fonts( $fonts ); if ( empty( $fonts ) ) { return; } $css = $this->get_css( $fonts ); $css = wp_strip_all_tags( $css ); if ( empty( $css ) ) { return; } printf( $this->get_style_element(), $css ); } private function validate_fonts( array $fonts ) { $validated_fonts = array(); foreach ( $fonts as $font_faces ) { foreach ( $font_faces as $font_face ) { $font_face = $this->validate_font_face_declarations( $font_face ); if ( false === $font_face ) { continue; } $validated_fonts[] = $font_face; } } return $validated_fonts; } private function validate_font_face_declarations( array $font_face ) { $font_face = wp_parse_args( $font_face, $this->font_face_property_defaults ); if ( empty( $font_face['font-family'] ) || ! is_string( $font_face['font-family'] ) ) { _doing_it_wrong( __METHOD__, __( 'Font font-family must be a non-empty string.' ), '6.4.0' ); return false; } if ( empty( $font_face['src'] ) || ( ! is_string( $font_face['src'] ) && ! is_array( $font_face['src'] ) ) ) { _doing_it_wrong( __METHOD__, __( 'Font src must be a non-empty string or an array of strings.' ), '6.4.0' ); return false; } foreach ( (array) $font_face['src'] as $src ) { if ( empty( $src ) || ! is_string( $src ) ) { _doing_it_wrong( __METHOD__, __( 'Each font src must be a non-empty string.' ), '6.4.0' ); return false; } } if ( ! is_string( $font_face['font-weight'] ) && ! is_int( $font_face['font-weight'] ) ) { _doing_it_wrong( __METHOD__, __( 'Font font-weight must be a properly formatted string or integer.' ), '6.4.0' ); return false; } if ( ! in_array( $font_face['font-display'], $this->valid_font_display, true ) ) { $font_face['font-display'] = $this->font_face_property_defaults['font-display']; } foreach ( $font_face as $property => $value ) { if ( ! in_array( $property, $this->valid_font_face_properties, true ) ) { unset( $font_face[ $property ] ); } } return $font_face; } private function get_style_element() { $attributes = $this->generate_style_element_attributes(); return "<style id='wp-fonts-local'{$attributes}>\n%s\n</style>\n"; } private function generate_style_element_attributes() { $attributes = ''; foreach ( $this->style_tag_attrs as $name => $value ) { $attributes .= " {$name}='{$value}'"; } return $attributes; } private function get_css( $font_faces ) { $css = ''; foreach ( $font_faces as $font_face ) { $font_face = $this->order_src( $font_face ); $css .= '@font-face{' . $this->build_font_face_css( $font_face ) . '}' . "\n"; } return rtrim( $css, "\n" ); } private function order_src( array $font_face ) { if ( ! is_array( $font_face['src'] ) ) { $font_face['src'] = (array) $font_face['src']; } $src = array(); $src_ordered = array(); foreach ( $font_face['src'] as $url ) { if ( str_starts_with( trim( $url ), 'data:' ) ) { $src_ordered[] = array( 'url' => $url, 'format' => 'data', ); continue; } $format = pathinfo( $url, PATHINFO_EXTENSION ); $src[ $format ] = $url; } if ( ! empty( $src['woff2'] ) ) { $src_ordered[] = array( 'url' => $src['woff2'], 'format' => 'woff2', ); } if ( ! empty( $src['woff'] ) ) { $src_ordered[] = array( 'url' => $src['woff'], 'format' => 'woff', ); } if ( ! empty( $src['ttf'] ) ) { $src_ordered[] = array( 'url' => $src['ttf'], 'format' => 'truetype', ); } if ( ! empty( $src['eot'] ) ) { $src_ordered[] = array( 'url' => $src['eot'], 'format' => 'embedded-opentype', ); } if ( ! empty( $src['otf'] ) ) { $src_ordered[] = array( 'url' => $src['otf'], 'format' => 'opentype', ); } $font_face['src'] = $src_ordered; return $font_face; } private function build_font_face_css( array $font_face ) { $css = ''; if ( str_contains( $font_face['font-family'], ' ' ) && ! str_contains( $font_face['font-family'], '"' ) && ! str_contains( $font_face['font-family'], "'" ) ) { $font_face['font-family'] = '"' . $font_face['font-family'] . '"'; } foreach ( $font_face as $key => $value ) { if ( 'src' === $key ) { $value = $this->compile_src( $value ); } if ( 'font-variation-settings' === $key && is_array( $value ) ) { $value = $this->compile_variations( $value ); } if ( ! empty( $value ) ) { $css .= "$key:$value;"; } } return $css; } private function compile_src( array $value ) { $src = ''; foreach ( $value as $item ) { $src .= ( 'data' === $item['format'] ) ? ", url({$item['url']})" : ", url('{$item['url']}') format('{$item['format']}')"; } $src = ltrim( $src, ', ' ); return $src; } private function compile_variations( array $font_variation_settings ) { $variations = ''; foreach ( $font_variation_settings as $key => $value ) { $variations .= "$key $value"; } return $variations; } } 