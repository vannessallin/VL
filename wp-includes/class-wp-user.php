<?php
 #[AllowDynamicProperties]
 class WP_User { public $data; public $ID = 0; public $caps = array(); public $cap_key; public $roles = array(); public $allcaps = array(); public $filter = null; private $site_id = 0; private static $back_compat_keys; public function __construct( $id = 0, $name = '', $site_id = '' ) { global $wpdb; if ( ! isset( self::$back_compat_keys ) ) { $prefix = $wpdb->prefix; self::$back_compat_keys = array( 'user_firstname' => 'first_name', 'user_lastname' => 'last_name', 'user_description' => 'description', 'user_level' => $prefix . 'user_level', $prefix . 'usersettings' => $prefix . 'user-settings', $prefix . 'usersettingstime' => $prefix . 'user-settings-time', ); } if ( $id instanceof WP_User ) { $this->init( $id->data, $site_id ); return; } elseif ( is_object( $id ) ) { $this->init( $id, $site_id ); return; } if ( ! empty( $id ) && ! is_numeric( $id ) ) { $name = $id; $id = 0; } if ( $id ) { $data = self::get_data_by( 'id', $id ); } else { $data = self::get_data_by( 'login', $name ); } if ( $data ) { $this->init( $data, $site_id ); } else { $this->data = new stdClass(); } } public function init( $data, $site_id = '' ) { if ( ! isset( $data->ID ) ) { $data->ID = 0; } $this->data = $data; $this->ID = (int) $data->ID; $this->for_site( $site_id ); } public static function get_data_by( $field, $value ) { global $wpdb; if ( 'ID' === $field ) { $field = 'id'; } if ( 'id' === $field ) { if ( ! is_numeric( $value ) ) { return false; } $value = (int) $value; if ( $value < 1 ) { return false; } } else { $value = trim( $value ); } if ( ! $value ) { return false; } switch ( $field ) { case 'id': $user_id = $value; $db_field = 'ID'; break; case 'slug': $user_id = wp_cache_get( $value, 'userslugs' ); $db_field = 'user_nicename'; break; case 'email': $user_id = wp_cache_get( $value, 'useremail' ); $db_field = 'user_email'; break; case 'login': $value = sanitize_user( $value ); $user_id = wp_cache_get( $value, 'userlogins' ); $db_field = 'user_login'; break; default: return false; } if ( false !== $user_id ) { $user = wp_cache_get( $user_id, 'users' ); if ( $user ) { return $user; } } $user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE $db_field = %s LIMIT 1", $value ) ); if ( ! $user ) { return false; } update_user_caches( $user ); return $user; } public function __isset( $key ) { if ( 'id' === $key ) { _deprecated_argument( 'WP_User->id', '2.1.0', sprintf( __( 'Use %s instead.' ), '<code>WP_User->ID</code>' ) ); $key = 'ID'; } if ( isset( $this->data->$key ) ) { return true; } if ( isset( self::$back_compat_keys[ $key ] ) ) { $key = self::$back_compat_keys[ $key ]; } return metadata_exists( 'user', $this->ID, $key ); } public function __get( $key ) { if ( 'id' === $key ) { _deprecated_argument( 'WP_User->id', '2.1.0', sprintf( __( 'Use %s instead.' ), '<code>WP_User->ID</code>' ) ); return $this->ID; } if ( isset( $this->data->$key ) ) { $value = $this->data->$key; } else { if ( isset( self::$back_compat_keys[ $key ] ) ) { $key = self::$back_compat_keys[ $key ]; } $value = get_user_meta( $this->ID, $key, true ); } if ( $this->filter ) { $value = sanitize_user_field( $key, $value, $this->ID, $this->filter ); } return $value; } public function __set( $key, $value ) { if ( 'id' === $key ) { _deprecated_argument( 'WP_User->id', '2.1.0', sprintf( __( 'Use %s instead.' ), '<code>WP_User->ID</code>' ) ); $this->ID = $value; return; } $this->data->$key = $value; } public function __unset( $key ) { if ( 'id' === $key ) { _deprecated_argument( 'WP_User->id', '2.1.0', sprintf( __( 'Use %s instead.' ), '<code>WP_User->ID</code>' ) ); } if ( isset( $this->data->$key ) ) { unset( $this->data->$key ); } if ( isset( self::$back_compat_keys[ $key ] ) ) { unset( self::$back_compat_keys[ $key ] ); } } public function exists() { return ! empty( $this->ID ); } public function get( $key ) { return $this->__get( $key ); } public function has_prop( $key ) { return $this->__isset( $key ); } public function to_array() { return get_object_vars( $this->data ); } public function __call( $name, $arguments ) { if ( '_init_caps' === $name ) { return $this->_init_caps( ...$arguments ); } return false; } protected function _init_caps( $cap_key = '' ) { global $wpdb; _deprecated_function( __METHOD__, '4.9.0', 'WP_User::for_site()' ); if ( empty( $cap_key ) ) { $this->cap_key = $wpdb->get_blog_prefix( $this->site_id ) . 'capabilities'; } else { $this->cap_key = $cap_key; } $this->caps = $this->get_caps_data(); $this->get_role_caps(); } public function get_role_caps() { $switch_site = false; if ( is_multisite() && get_current_blog_id() !== $this->site_id ) { $switch_site = true; switch_to_blog( $this->site_id ); } $wp_roles = wp_roles(); if ( is_array( $this->caps ) ) { $this->roles = array_filter( array_keys( $this->caps ), array( $wp_roles, 'is_role' ) ); } $this->allcaps = array(); foreach ( (array) $this->roles as $role ) { $the_role = $wp_roles->get_role( $role ); $this->allcaps = array_merge( (array) $this->allcaps, (array) $the_role->capabilities ); } $this->allcaps = array_merge( (array) $this->allcaps, (array) $this->caps ); if ( $switch_site ) { restore_current_blog(); } return $this->allcaps; } public function add_role( $role ) { if ( empty( $role ) ) { return; } if ( in_array( $role, $this->roles, true ) ) { return; } $this->caps[ $role ] = true; update_user_meta( $this->ID, $this->cap_key, $this->caps ); $this->get_role_caps(); $this->update_user_level_from_caps(); do_action( 'add_user_role', $this->ID, $role ); } public function remove_role( $role ) { if ( ! in_array( $role, $this->roles, true ) ) { return; } unset( $this->caps[ $role ] ); update_user_meta( $this->ID, $this->cap_key, $this->caps ); $this->get_role_caps(); $this->update_user_level_from_caps(); do_action( 'remove_user_role', $this->ID, $role ); } public function set_role( $role ) { if ( 1 === count( $this->roles ) && current( $this->roles ) === $role ) { return; } foreach ( (array) $this->roles as $oldrole ) { unset( $this->caps[ $oldrole ] ); } $old_roles = $this->roles; if ( ! empty( $role ) ) { $this->caps[ $role ] = true; $this->roles = array( $role => true ); } else { $this->roles = array(); } update_user_meta( $this->ID, $this->cap_key, $this->caps ); $this->get_role_caps(); $this->update_user_level_from_caps(); foreach ( $old_roles as $old_role ) { if ( ! $old_role || $old_role === $role ) { continue; } do_action( 'remove_user_role', $this->ID, $old_role ); } if ( $role && ! in_array( $role, $old_roles, true ) ) { do_action( 'add_user_role', $this->ID, $role ); } do_action( 'set_user_role', $this->ID, $role, $old_roles ); } public function level_reduction( $max, $item ) { if ( preg_match( '/^level_(10|[0-9])$/i', $item, $matches ) ) { $level = (int) $matches[1]; return max( $max, $level ); } else { return $max; } } public function update_user_level_from_caps() { global $wpdb; $this->user_level = array_reduce( array_keys( $this->allcaps ), array( $this, 'level_reduction' ), 0 ); update_user_meta( $this->ID, $wpdb->get_blog_prefix() . 'user_level', $this->user_level ); } public function add_cap( $cap, $grant = true ) { $this->caps[ $cap ] = $grant; update_user_meta( $this->ID, $this->cap_key, $this->caps ); $this->get_role_caps(); $this->update_user_level_from_caps(); } public function remove_cap( $cap ) { if ( ! isset( $this->caps[ $cap ] ) ) { return; } unset( $this->caps[ $cap ] ); update_user_meta( $this->ID, $this->cap_key, $this->caps ); $this->get_role_caps(); $this->update_user_level_from_caps(); } public function remove_all_caps() { global $wpdb; $this->caps = array(); delete_user_meta( $this->ID, $this->cap_key ); delete_user_meta( $this->ID, $wpdb->get_blog_prefix() . 'user_level' ); $this->get_role_caps(); } public function has_cap( $cap, ...$args ) { if ( is_numeric( $cap ) ) { _deprecated_argument( __FUNCTION__, '2.0.0', __( 'Usage of user levels is deprecated. Use capabilities instead.' ) ); $cap = $this->translate_level_to_cap( $cap ); } $caps = map_meta_cap( $cap, $this->ID, ...$args ); if ( is_multisite() && is_super_admin( $this->ID ) ) { if ( in_array( 'do_not_allow', $caps, true ) ) { return false; } return true; } $args = array_merge( array( $cap, $this->ID ), $args ); $capabilities = apply_filters( 'user_has_cap', $this->allcaps, $caps, $args, $this ); $capabilities['exist'] = true; unset( $capabilities['do_not_allow'] ); foreach ( (array) $caps as $cap ) { if ( empty( $capabilities[ $cap ] ) ) { return false; } } return true; } public function translate_level_to_cap( $level ) { return 'level_' . $level; } public function for_blog( $blog_id = '' ) { _deprecated_function( __METHOD__, '4.9.0', 'WP_User::for_site()' ); $this->for_site( $blog_id ); } public function for_site( $site_id = '' ) { global $wpdb; if ( ! empty( $site_id ) ) { $this->site_id = absint( $site_id ); } else { $this->site_id = get_current_blog_id(); } $this->cap_key = $wpdb->get_blog_prefix( $this->site_id ) . 'capabilities'; $this->caps = $this->get_caps_data(); $this->get_role_caps(); } public function get_site_id() { return $this->site_id; } private function get_caps_data() { $caps = get_user_meta( $this->ID, $this->cap_key, true ); if ( ! is_array( $caps ) ) { return array(); } return $caps; } } 