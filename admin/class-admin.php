<?php

if( ! function_exists( 'add_action' ) )
	exit;

if( ! class_exists( 'PD_Discord_OAuth_Admin' ) ){
	
	class PD_Discord_OAuth_Admin{
		
		private static $plugin_path;
		private static $plugin_url;
		private static $plugin_version;
		
		public function __construct(){
			
			require( 'class-admin-menu.php' );
			
			self::$plugin_path = PD_DISCORD_OAUTH_PLUGIN_DIR_PATH;
			self::$plugin_url = PD_DISCORD_OAUTH_PLUGIN_URL;
			self::$plugin_version = PD_DISCORD_OAUTH_PLUGIN_VERSION;
			
			$saved_oauth2_configuration = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			$saved_bot_configuration = maybe_unserialize( get_option( '_pd_discord_bot_configuration_settings' ) );
			
			if( ! isset( $_POST['method'] ) || ( isset( $_POST['method'] ) && sanitize_text_field( $_POST['method'] ) != 'pd_update_oauth2_configuration' ) ){
				
				if( ! $saved_oauth2_configuration ){
				
					add_action( 'admin_notices', function(){ ?>
						<div class="notice notice-warning is-dismissible">
							<?php printf( '<p>%s <a href="%s" class="button button-secondary button-small">%s</a></p>', __( 'OAuth2 settings for SSO OAuth for Discord by path digital are not yet configured. To enable the plugin, please update' ), 'admin.php?page=pd_discord_oauth_settings&tab=bot', __( 'OAuth2 Settings' ) ); ?>
						</div>
					<?php } );
					
				}else if( ! isset( $saved_oauth2_configuration['settings_version'] ) || version_compare( $saved_oauth2_configuration['settings_version'], '1.2.0' ) < 0 ){
				
					add_action( 'admin_notices', function(){ ?>
						<div class="notice notice-error is-dismissible">
							<?php printf( '<p>%s <a href="%s" class="button button-secondary button-small">%s</a></p>', __( 'OAuth2 settings for SSO OAuth for Discord by path digital are out of date. To enable new features, please update' ), 'admin.php?page=pd_discord_oauth_settings&tab=bot', __( 'OAuth2 Settings' ) ); ?>
						</div>
					<?php } );
					
				}
				
			}
			
			if( ! isset( $_POST['method'] ) || ( isset( $_POST['method'] ) && sanitize_text_field( $_POST['method'] ) != 'pd_update_bot_configuration' ) ){
				
				if( ! $saved_bot_configuration  ){
				
					add_action( 'admin_notices', function(){ ?>
						<div class="notice notice-warning is-dismissible">
							<?php printf( '<p>%s <a href="%s" class="button button-secondary button-small">%s</a></p>', __( 'Bot settings for SSO OAuth for Discord by path digital are not yet configured. To enable user role validation, please update' ), 'admin.php?page=pd_discord_oauth_settings&tab=bot', __( 'Bot Settings' ) ); ?>
						</div>
					<?php } );
					
				}else if( ! isset( $saved_bot_configuration['settings_version'] ) ||  version_compare( $saved_bot_configuration['settings_version'], '1.2.0' ) < 0 ){
				
					add_action( 'admin_notices', function(){ ?>
						<div class="notice notice-error is-dismissible">
							<?php printf( '<p>%s <a href="%s" class="button button-secondary button-small">%s</a></p>', __( 'Bot settings for SSO OAuth for Discord by path digital are out of date. To enable new features, please update' ), 'admin.php?page=pd_discord_oauth_settings&tab=bot', __( 'Bot Settings' ) ); ?>
						</div>
					<?php } );
					
				}
				
			}
			
        }

		/** Registering styles for the admin area **/
		public static function enqueue_styles( $admin_page ){
			
			if( $admin_page != 'toplevel_page_pd_discord_oauth_settings' )
				return;
			
			wp_enqueue_style( 'pathdigital-discord-oauth',
				self::$plugin_url . '/admin/css/style.css', array(),
				self::$plugin_version
			);

		}

		/** Registering scripts for the admin area **/
		public static function enqueue_scripts( $admin_page ){
			
			if( $admin_page != 'toplevel_page_pd_discord_oauth_settings' )
				return;
			
			wp_enqueue_script( 'pathdigital-discord-oauth',
				self::$plugin_url . '/admin/js/script.js',
				array( 'jquery' ),
				self::$plugin_version,
				true
			);
			
			wp_localize_script( 'pathdigital-discord-auth',
				'pathdigital_discord_oauth_ajax',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'plugin_url' => self::$plugin_url )
			);
			
		}
		
		/** Registering admin menu **/
		public static function admin_menu(){
			
			add_menu_page( 'path digital Discord OAuth Settings', 'Discord OAuth', 'administrator', 'pd_discord_oauth_settings', array( 'PD_Discord_OAuth_Admin_Menu', 'render_menu' ), plugin_dir_url( __FILE__ ) . 'images/icon_16x16.png', 99 );
			
			global $submenu;
			unset( $submenu['pd_discord_auth_settings'][0] );
				
		}
		
		/** Adding the settings link **/
		public static function settings_link( $links ){
			
			$url = esc_url( add_query_arg(
				'page',
				'pd_discord_oauth_settings',
				get_admin_url() . 'admin.php'
			) );
			
			array_push(
				$links,
				'<a href="' . $url . '">' . __( 'Settings' ) . '</a>'
			);
			
			return $links;
		}
		
		/** Updating settings **/
		public function update_settings(){
			
			if( isset( $_POST['method'] ) && sanitize_text_field( wp_unslash( $_POST['method'] ) ) == 'pd_update_oauth2_configuration' ){
				
				if( isset( $_REQUEST['pd_update_oauth2_configuration_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pd_update_oauth2_configuration_form_field'] ) ), 'pd_update_oauth2_configuration_form' ) ){
					
					if( current_user_can( 'administrator' ) ){
						
						$login_page = isset( $_POST['pd_login_page'] ) ? intval( $_POST['pd_login_page'] ) : 0;
						$redirect_url = isset( $_POST['pd_redirect_url'] ) ? esc_url_raw( trim( $_POST['pd_redirect_url'] ) ) : '';
						$client_id =  isset( $_POST['pd_client_id'] ) ? sanitize_text_field( trim( $_POST['pd_client_id'] ) ) : '';
						$client_secret = isset( $_POST['pd_client_secret'] ) ? sanitize_text_field( trim( $_POST['pd_client_secret'] ) ) : '';
						$scope = ( isset( $_POST['pd_scope'] ) && $_POST['pd_scope'] != '' ) ? sanitize_text_field( trim( $_POST['pd_scope'] ) ) : 'identify guilds';
						$base_authorization_url = isset( $_POST['pd_authorization_url'] ) ? esc_url_raw( trim( $_POST['pd_authorization_url'] ) ) : '';
						$token_url = isset( $_POST['pd_token_url'] ) ? esc_url_raw( trim( $_POST['pd_token_url'] ) ) : '';
						$user_info_endpoint = isset( $_POST['pd_user_info_endpoint'] ) ? esc_url_raw( trim( $_POST['pd_user_info_endpoint'] ) ) : '';
						$user_guilds_endpoint = isset( $_POST['pd_user_guilds_endpoint'] ) ? esc_url_raw( trim( $_POST['pd_user_guilds_endpoint'] ) ) : '';
						$server_validation = isset( $_POST['pd_server_validation'] ) && esc_attr( $_POST['pd_server_validation'] ) == 1 ? true : false;
						$server_ids = isset( $_POST['pd_servers'] ) ? sanitize_textarea_field( trim( $_POST['pd_servers'] ) ) : '';
						$server_ids_blocked = isset( $_POST['pd_servers_blocked'] ) ? sanitize_textarea_field( trim( $_POST['pd_servers_blocked'] ) ) : '';
						$user_validation = isset( $_POST['pd_user_validation'] ) && esc_attr( $_POST['pd_user_validation'] ) == 1 ? true : false;
						$user_ids = isset( $_POST['pd_users'] ) ? sanitize_textarea_field( trim( $_POST['pd_users'] ) ) : '';
						
						/** Cheking all the inputs for empty values **/
						if( empty( $login_page ) || empty( $redirect_url ) || empty( $client_id ) || empty( $client_secret ) || empty( $base_authorization_url ) || empty( $token_url ) || empty( $user_info_endpoint ) || empty( $user_guilds_endpoint ) ){
							
							update_option( '_pd_discord_oauth_notice', 'Please fill in all the required fields.' );
							pd_discord_oauth_show_admin_notice( 'error' );
							return;
							
						}
						
						/** Client ID should be integer  **/
						if( filter_var( $client_id, FILTER_VALIDATE_INT ) === FALSE ){
							
							update_option( '_pd_discord_oauth_notice', 'Please enter a valid Client ID.' );
							pd_discord_oauth_show_admin_notice( 'error' );
							return;
							
						}
						
						/** Scope should be alphabetic */
						if( ctype_alpha( str_replace( ' ', '', $scope ) ) === FALSE ){
							
							update_option( '_pd_discord_oauth_notice', 'Please enter a valid scope.' );
							pd_discord_oauth_show_admin_notice( 'error' );
							return;
							
						}
						
						$server_ids = str_replace( ',', PHP_EOL, $server_ids );
						$server_ids = explode( PHP_EOL, $server_ids );
						$server_ids_array = array_filter( array_map( 'trim', $server_ids ) );
						
						foreach( $server_ids_array as $server_id ){
							
							/** Server ID should be integer  **/
							if( filter_var( $server_id, FILTER_VALIDATE_INT ) === FALSE ){
								
								update_option( '_pd_discord_oauth_notice', 'Please enter a valid server ID\'s for allowed servers.' );
								pd_discord_oauth_show_admin_notice( 'error' );
								return;
								
							}
							
						}
						
						$server_ids_blocked = str_replace( ',', PHP_EOL, $server_ids_blocked );
						$server_ids_blocked = explode( PHP_EOL, $server_ids_blocked );
						$server_ids_blocked_array = array_filter( array_map( 'trim', $server_ids_blocked ) );
						
						foreach( $server_ids_blocked_array as $server_id_blocked ){
							
							/** Server ID should be integer  **/
							if( filter_var( $server_id_blocked, FILTER_VALIDATE_INT ) === FALSE ){
								
								update_option( '_pd_discord_oauth_notice', 'Please enter a valid server ID\'s for blocked servers.' );
								pd_discord_oauth_show_admin_notice( 'error' );
								return;
								
							}
							
						}
						
						$user_ids = str_replace( ',', PHP_EOL, $user_ids );
						$user_ids = explode( PHP_EOL, $user_ids );
						$user_ids_array = array_filter( array_map( 'trim', $user_ids ) );
						
						foreach( $user_ids_array as $user_id ){
							
							/** User ID should be integer  **/
							if( filter_var( $user_id, FILTER_VALIDATE_INT ) === FALSE ){
								
								update_option( '_pd_discord_oauth_notice', 'Please enter a valid user ID\'s for blocked users.' );
								pd_discord_oauth_show_admin_notice( 'error' );
								return;
								
							}
							
						}
						
						$settings = array(
							'login_page' => $login_page,
							'redirect_url' => $redirect_url,
							'client_id' => $client_id,
							'client_secret' => $client_secret,
							'scope' => $scope,
							'base_authorization_url' => $base_authorization_url,
							'token_url' => $token_url,
							'user_info_endpoint' => $user_info_endpoint,
							'user_guilds_endpoint' => $user_guilds_endpoint,
							'server_validation' => $server_validation,
							'server_ids' => $server_ids_array,
							'server_ids_blocked' => $server_ids_blocked_array,
							'user_validation' => $user_validation,
							'user_ids' => $user_ids_array,
							'settings_version' => self::$plugin_version
						);
						
						update_option( '_pd_discord_oauth_configuration_settings', maybe_serialize( $settings ) );
						update_option( '_pd_discord_oauth_notice', 'Settings saved successfully.' );
						pd_discord_oauth_show_admin_notice( 'success' );
						
					}else{
						
						update_option( '_pd_discord_oauth_notice', 'You don\'t have permission.' );
						pd_discord_oauth_show_admin_notice( 'error' );
						
					}
					
				}else{
						
					update_option( '_pd_discord_oauth_notice', 'Something went wrong. Please try again.' );
					pd_discord_oauth_show_admin_notice( 'error' );
					
				}
				
			}
			
			if( isset( $_POST['method'] ) && sanitize_text_field( wp_unslash( $_POST['method'] ) ) == 'pd_update_bot_configuration' ){
				
				if( isset( $_REQUEST['pd_update_bot_configuration_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pd_update_bot_configuration_form_field'] ) ), 'pd_update_bot_configuration_form' ) ){
					
					if( current_user_can( 'administrator' ) ){
						
						$bot_token = isset( $_POST['pd_bot_tolken'] ) ? sanitize_text_field( trim( $_POST['pd_bot_tolken'] ) ) : '';
						$role_validation = isset( $_POST['pd_role_validation'] ) && esc_attr( $_POST['pd_role_validation'] ) == 1 ? true : false;
						$validation_condition = isset( $_POST['pd_validation_condition'] ) ? esc_attr( $_POST['pd_validation_condition'] ) : 'one';
						$server_roles = isset( $_POST['pd_server'] ) ? ( array ) $_POST['pd_server'] : array();
						$allowed_roles = isset( $_POST['pd_roles'] ) ? ( array ) $_POST['pd_roles'] : array();
						$webhook = isset( $_POST['pd_webhook'] ) ? esc_url( $_POST['pd_webhook'] ) : '';
						$bot_icon = isset( $_POST['pd_bot_icon'] ) ? esc_url( $_POST['pd_bot_icon'] ) : '';
						$embed_color = isset( $_POST['pd_embed_color'] ) ? esc_url( $_POST['pd_embed_color'] ) : '';
						$all_server_roles = isset( $_POST['pd_all_server_roles'] ) ? base64_decode( $_POST['pd_all_server_roles'] ) : '';
						
						/** Cheking all the inputs for empty values **/
						if( empty( $bot_token ) ){
							
							update_option( '_pd_discord_oauth_notice', 'Please fill in all the required fields.' );
							pd_discord_oauth_show_admin_notice( 'error' );
							return;
							
						}
						
						/** Webhook should be a URL **/
						if( $webhook != '' && filter_var( $webhook, FILTER_VALIDATE_URL ) === FALSE ){
							
							update_option( '_pd_discord_oauth_notice', 'Please enter a valid webhook URL.' );
							pd_discord_oauth_show_admin_notice( 'error' );
							return;
							
						}
						
						/** Bot icon should be a URL **/
						if( $bot_icon != '' && filter_var( $bot_icon, FILTER_VALIDATE_URL ) === FALSE ){
							
							update_option( '_pd_discord_oauth_notice', 'Please enter a valid bot icon URL.' );
							pd_discord_oauth_show_admin_notice( 'error' );
							return;
							
						}
						
						$settings = array(
							'bot_token' => $bot_token,
							'role_validation' => $role_validation,
							'validation_condition' => $validation_condition,
							'server_roles' => $server_roles,
							'allowed_roles' => $allowed_roles,
							'webhook' => $webhook,
							'bot_icon' => $bot_icon,
							'embed_color' => $embed_color,
							'settings_version' => self::$plugin_version
						);
						
						update_option( '_pd_discord_bot_configuration_settings', maybe_serialize( $settings ) );
						update_option( '_pd_discord_all_server_roles', $all_server_roles );
						update_option( '_pd_discord_oauth_notice', 'Settings saved successfully.' );
						pd_discord_oauth_show_admin_notice( 'success' );
						
					}else{
						
						update_option( '_pd_discord_oauth_notice', 'You don\'t have permission.' );
						pd_discord_oauth_show_admin_notice( 'error' );
						
					}
					
				}else{
						
					update_option( '_pd_discord_oauth_notice', 'Something went wrong. Please try again.' );
					pd_discord_oauth_show_admin_notice( 'error' );
					
				}
				
			}
			
			if( isset( $_POST['method'] ) && sanitize_text_field( wp_unslash( $_POST['method'] ) ) == 'pd_update_general_configuration' ){
				
				if( isset( $_REQUEST['pd_update_general_configuration_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pd_update_general_configuration_form_field'] ) ), 'pd_update_general_configuration_form' ) ){
					
					if( current_user_can( 'administrator' ) ){
						
						$excluded_pages = isset( $_POST['pd_excluded_pages'] ) ?  array_map( 'sanitize_text_field', $_POST['pd_excluded_pages'] ) : array();
						$excluded_posts = isset( $_POST['pd_excluded_posts'] ) ?  array_map( 'sanitize_text_field', $_POST['pd_excluded_posts'] ) : array();
						$hide_menus = isset( $_POST['pd_menus'] ) && esc_attr( $_POST['pd_menus'] ) == 1 ? true : false;
						$hide_widgets = isset( $_POST['pd_widgets'] ) && esc_attr( $_POST['pd_widgets'] ) == 1 ? true : false;
						$session_expiry = isset( $_POST['pd_session_expiry'] ) && sanitize_text_field( $_POST['pd_session_expiry'] ) != '' ? sanitize_text_field( $_POST['pd_session_expiry'] ) : 3;
						$success_redirect_page = isset( $_POST['pd_success_redirect_page'] ) ? intval( $_POST['pd_success_redirect_page'] ) : 0;
						$button_styles = isset( $_POST['pd_login_button_styles'] ) ? sanitize_textarea_field( trim( $_POST['pd_login_button_styles'] ) ) : '';
						$button_styles = str_replace( ';', PHP_EOL, $button_styles );
						$button_styles = explode( PHP_EOL, $button_styles );
						$button_styles_array = array_filter( array_map( 'trim', $button_styles ) );
						
						/** Session duration should be integer  **/
						if( filter_var( $session_expiry, FILTER_VALIDATE_INT ) === FALSE ){
							
							update_option( '_pd_discord_oauth_notice', 'Session duration must be an integer or blank.' );
							pd_discord_oauth_show_admin_notice( 'error' );
							return;
							
						}
						
						/** Session duration should be 0 to 7 **/
						if( $session_expiry > 7 || $session_expiry < 0 ){
							
							update_option( '_pd_discord_oauth_notice', 'Session duration should be 0 to 7.' );
							pd_discord_oauth_show_admin_notice( 'error' );
							return;
							
						}
						
						$settings = array(
							'excluded_pages' => array_filter( $excluded_pages, 'strlen' ),
							'excluded_posts' => array_filter( $excluded_posts, 'strlen' ),
							'hide_menus' => $hide_menus,
							'hide_widgets' => $hide_widgets,
							'session_expiry' => $session_expiry,
							'success_redirect_page' => $success_redirect_page,
							'login_button_styles' => $button_styles_array,
							'settings_version' => self::$plugin_version
						);
						
						update_option( '_pd_discord_oauth_general_settings', maybe_serialize( $settings ) );
						update_option( '_pd_discord_oauth_notice', 'Settings saved successfully.' );
						pd_discord_oauth_show_admin_notice( 'success' );
						
					}else{
						
						update_option( '_pd_discord_oauth_notice', 'You don\'t have permission.' );
						pd_discord_oauth_show_admin_notice( 'error' );
						
					}
					
				}else{
						
					update_option( '_pd_discord_oauth_notice', 'Something went wrong. Please try again.' );
					pd_discord_oauth_show_admin_notice( 'error' );
					
				}
				
			}
			
			if( isset( $_POST['method'] ) && sanitize_text_field( wp_unslash( $_POST['method'] ) ) == 'pd_update_login_page_configuration' ){
				
				if( isset( $_REQUEST['pd_update_login_page_configuration_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pd_update_login_page_configuration_form_field'] ) ), 'pd_update_login_page_configuration_form' ) ){
					
					if( current_user_can( 'administrator' ) ){
						
						$login_page = isset( $_POST['pd_login_page'] ) && esc_attr( $_POST['pd_login_page'] ) == 1 ? true : false;
						$logo = isset( $_POST['pd_login_page_logo'] ) && sanitize_url( $_POST['pd_login_page_logo'] ) != '' ? sanitize_url( $_POST['pd_login_page_logo'] ) : '';
						$heading = isset( $_POST['pd_login_page_heading'] ) && sanitize_url( $_POST['pd_login_page_heading'] ) != '' ? sanitize_textarea_field( $_POST['pd_login_page_heading'] ) : '';
						$subtext = isset( $_POST['pd_login_page_subtext'] ) && sanitize_url( $_POST['pd_login_page_subtext'] ) != '' ? sanitize_textarea_field( $_POST['pd_login_page_subtext'] ) : '';
						$background_color = isset( $_POST['pd_login_page_background_color'] ) && sanitize_text_field( $_POST['pd_login_page_background_color'] ) != '' ? sanitize_text_field( $_POST['pd_login_page_background_color'] ) : '';
						$background_image = isset( $_POST['pd_login_page_background_image'] ) && sanitize_url( $_POST['pd_login_page_background_image'] ) != '' ? sanitize_text_field( $_POST['pd_login_page_background_image'] ) : '';
						$banned_error = isset( $_POST['pd_login_page_banned_error'] ) && sanitize_url( $_POST['pd_login_page_banned_error'] ) != '' ? sanitize_text_field( $_POST['pd_login_page_banned_error'] ) : '';
						$no_role_error = isset( $_POST['pd_login_page_no_role_error'] ) && sanitize_url( $_POST['pd_login_page_no_role_error'] ) != '' ? sanitize_text_field( $_POST['pd_login_page_no_role_error'] ) : '';
						$not_in_server_error = isset( $_POST['pd_login_page_not_in_server_error'] ) && sanitize_url( $_POST['pd_login_page_not_in_server_error'] ) != '' ? sanitize_text_field( $_POST['pd_login_page_not_in_server_error'] ) : '';
						
						$settings = array(
							'login_page' => $login_page,
							'logo' => $logo,
							'heading' => $heading,
							'subtext' => $subtext,
							'background_color' => $background_color,
							'background_image' => $background_image,
							'banned_error' => $banned_error,
							'not_in_server_error' => $not_in_server_error,
							'no_role_error' => $no_role_error,
							'settings_version' => self::$plugin_version
						);
						
						update_option( '_pd_discord_oauth_login_page_settings', maybe_serialize( $settings ) );
						update_option( '_pd_discord_oauth_notice', 'Settings saved successfully.' );
						pd_discord_oauth_show_admin_notice( 'success' );
						
					}else{
						
						update_option( '_pd_discord_oauth_notice', 'You don\'t have permission.' );
						pd_discord_oauth_show_admin_notice( 'error' );
						
					}
					
				}else{
						
					update_option( '_pd_discord_oauth_notice', 'Something went wrong. Please try again.' );
					pd_discord_oauth_show_admin_notice( 'error' );
					
				}
				
			}
			
			if( isset( $_POST['method'] ) && sanitize_text_field( wp_unslash( $_POST['method'] ) ) == 'pd_update_maintainance_page_configuration' ){
				
				if( isset( $_REQUEST['pd_update_maintainance_page_configuration_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pd_update_maintainance_page_configuration_form_field'] ) ), 'pd_update_maintainance_page_configuration_form' ) ){
					
					if( current_user_can( 'administrator' ) ){
						
						$maintainance_mode = isset( $_POST['pd_maintainance_mode'] ) && esc_attr( $_POST['pd_maintainance_mode'] ) == 1 ? true : false;
						$logo = isset( $_POST['pd_maintainance_page_logo'] ) && sanitize_url( $_POST['pd_maintainance_page_logo'] ) != '' ? sanitize_url( $_POST['pd_maintainance_page_logo'] ) : '';
						$heading = isset( $_POST['pd_maintainance_page_heading'] ) && sanitize_url( $_POST['pd_maintainance_page_heading'] ) != '' ? sanitize_textarea_field( $_POST['pd_maintainance_page_heading'] ) : '';
						$subtext = isset( $_POST['pd_maintainance_page_subtext'] ) && sanitize_url( $_POST['pd_maintainance_page_subtext'] ) != '' ? sanitize_textarea_field( $_POST['pd_maintainance_page_subtext'] ) : '';
						$background_color = isset( $_POST['pd_maintainance_page_background_color'] ) && sanitize_text_field( $_POST['pd_maintainance_page_background_color'] ) != '' ? sanitize_text_field( $_POST['pd_maintainance_page_background_color'] ) : '';
						$background_image = isset( $_POST['pd_maintainance_page_background_image'] ) && sanitize_url( $_POST['pd_maintainance_page_background_image'] ) != '' ? sanitize_text_field( $_POST['pd_maintainance_page_background_image'] ) : '';
						
						$settings = array(
							'maintainance_mode' => $maintainance_mode,
							'logo' => $logo,
							'heading' => $heading,
							'subtext' => $subtext,
							'background_color' => $background_color,
							'background_image' => $background_image,
							'settings_version' => self::$plugin_version
						);
						
						update_option( '_pd_discord_oauth_maintainance_page_settings', maybe_serialize( $settings ) );
						update_option( '_pd_discord_oauth_notice', 'Settings saved successfully.' );
						pd_discord_oauth_show_admin_notice( 'success' );
						
					}else{
						
						update_option( '_pd_discord_oauth_notice', 'You don\'t have permission.' );
						pd_discord_oauth_show_admin_notice( 'error' );
						
					}
					
				}else{
						
					update_option( '_pd_discord_oauth_notice', 'Something went wrong. Please try again.' );
					pd_discord_oauth_show_admin_notice( 'error' );
					
				}
				
			}
			
			if( isset( $_POST['method'] ) && sanitize_text_field( wp_unslash( $_POST['method'] ) ) == 'pd_clear_login_sessions' ){
				
				if( isset( $_REQUEST['pd_clear_login_sessions_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pd_clear_login_sessions_form_field'] ) ), 'pd_clear_login_sessions_form' ) ){
					
					if( current_user_can( 'administrator' ) ){
						
						delete_option( '_pd_discord_oauth_user_sessions' );
						update_option( '_pd_discord_oauth_notice', 'Sessions cleared successfully.' );
						pd_discord_oauth_show_admin_notice( 'success' );
						
					}else{
						
						update_option( '_pd_discord_oauth_notice', 'You don\'t have permission.' );
						pd_discord_oauth_show_admin_notice( 'error' );
						
					}
					
				}else{
						
					update_option( '_pd_discord_oauth_notice', 'Something went wrong. Please try again.' );
					pd_discord_oauth_show_admin_notice( 'error' );
					
				}
				
			}
			
		}
		
		/** Add meta boxes **/
		public function add_meta_boxes(){
			add_meta_box(
				'pd_user_roles',
				'Discord OAuth',
				function(){
					$all_server_roles = maybe_unserialize( get_option( '_pd_discord_all_server_roles' ) );
					$selected_roles = get_post_meta( get_the_ID(), '_pd_page_accessible_roles', true );
					if( $all_server_roles ){
						echo '<p>Who can see this page?</p>';
						echo '<select name="pd_page_accessible_roles[]" id="pd_page_accessible_roles" class="components-text-control__input" style="box-sizing: border-box;">';
							echo '<option value=""' . ( ! $selected_roles ? ' selected="selected"' : '' ) . '>@everyone</option>';
						foreach( $all_server_roles as $server ){
							if( $server['roles'] ){
								echo '<optgroup label="' . ( $server['name'] ? $server['name'] : $server['id'] ) . '" style="font-style: normal;">';
									foreach( $server['roles'] as $server_role ){
										echo '<option value="' . $server['id'] . ';' . $server_role['id'] . '"' . ( isset( $selected_roles[$server['id']] ) && in_array( $server_role['id'], $selected_roles[$server['id']] ) ? ' selected="selected"' : '' ) . '>' . $server_role['name'] . '</option>';
									}
								echo '</optgroup>';
							}
						}
						echo '</select>';
						echo '<p class="description">Users with selected user role or upper will be able to view the page</p>';
					}else{
						echo '<p style="color: red;">Please see `Discord OAuth > Bot Settings`, add Bot to the server and save changes</p>';
					}
					wp_nonce_field( 'pd_page_accessible_roles_metabox_nonce', 'pd_page_accessible_roles_nonce' );
				},
				'page',
				'side',
				'high'
			);
		}
		
		/** Save meta boxes **/
		public function save_post_meta( $post_id ){
			
			if( ! isset( $_POST['pd_page_accessible_roles_nonce'] ) || ! wp_verify_nonce( $_POST['pd_page_accessible_roles_nonce'], 'pd_page_accessible_roles_metabox_nonce' ) ) 
				return;

			if( ! current_user_can( 'edit_post', $post_id ) )
				return;
			
			if( isset( $_POST['pd_page_accessible_roles'] ) ){
				
				$page_accessible_roles = array_map( 'strip_tags', $_POST['pd_page_accessible_roles'] );
				
				if( ! array_filter( $page_accessible_roles ) ){
					
					delete_post_meta( $post_id, '_pd_page_accessible_roles' );
					return;
					
				}
					
				$page_accessible_roles_array = array();
				
				foreach( $page_accessible_roles as $page_accessible_role ){
					$key_value = explode( ';', $page_accessible_role );
					$page_accessible_roles_array[$key_value[0]][] = $key_value[1];
				}
				
				update_post_meta( $post_id, '_pd_page_accessible_roles', $page_accessible_roles_array );
				
			}
		}
		
		/** Ban/unban user **/
		public function ban_user(){
			
			if( isset( $_POST['user'] ) ) {
				
				$result = $updated = false;
				$user_id = $_POST['user'];
				$oauth_configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
				$blocked_users = $oauth_configuration_settings['user_ids'];
				
				if( in_array( $user_id, $blocked_users ) ){
					foreach( $oauth_configuration_settings['user_ids'] as $key => $value )
						if( $value == $user_id )
							unset( $oauth_configuration_settings['user_ids'][$key] );
						
					$result = 'unblocked';
				}else{
					$oauth_configuration_settings['user_ids'][] = $user_id;
					$result = 'blocked';
				}
				
				if( update_option( '_pd_discord_oauth_configuration_settings', maybe_serialize( $oauth_configuration_settings ) ) )
					$updated = true;
				
			}
			
			echo json_encode( array( 'updated' => $updated, 'result' => $result ) );
			wp_die();
			
		}
		
		/** Force logout **/
		public function force_logout(){
			
			if( isset( $_POST['user'] ) ) {
				
				$updated = false;
				$user_id = $_POST['user'];
				$user_sessions = maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) );
				
				foreach( $user_sessions as $key => $value )
					foreach( $value as $key2 => $value2 )
						if( isset( $value2['id'] ) && $value2['id'] == $user_id )
							unset( $user_sessions[$key] );
				
				if( update_option( '_pd_discord_oauth_user_sessions', maybe_serialize( $user_sessions ) ) )
					$updated = true;
				
			}
			
			echo json_encode( array( 'updated' => $updated ) );
			wp_die();
			
		}
		
	}

}

?>