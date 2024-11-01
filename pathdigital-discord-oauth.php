<?php
/**
 * Plugin Name: SSO OAuth for Discord by path digital
 * Description: Discord OAuth for your Website. Hide your website content with Discord SSO and make it only available for your server members.  
 * Version: 3.1.3
 * Author: path digital
 * Author URI: https://pathdigital.de/
 * Tags: Discord, SSO, Login, OAuth, Discord Login, Social Login
 * License: GPLv2 or later
*/

if( ! function_exists( 'add_action' ) )
	exit;

if( ! class_exists( 'PD_Discord_OAuth' ) ){
	
	class PD_Discord_OAuth{
		
		private static $plugin_path;
		private static $plugin_url;
		private static $plugin_version;
		private static $authenticated;
		
		/** On plugin activation **/
		public function activation(){
			
			if( ! get_option( '_pd_discord_oauth_configuration_settings' ) )
				add_option( '_pd_discord_oauth_configuration_settings', maybe_serialize( array( ) ) );
			
			if( ! get_option( '_pd_discord_bot_configuration_settings' ) )
				add_option( '_pd_discord_bot_configuration_settings', maybe_serialize( array( ) ) );
			
			if( ! get_option( '_pd_discord_oauth_general_settings' ) )
				add_option( '_pd_discord_oauth_general_settings', maybe_serialize( array( ) ) );
				
			
		}
		
		/** Running the plugin **/
		public static function run(){
			
			require( 'constants.php' );
			require( 'admin/class-admin.php' );
			require( 'includes/class-discord.php' );
			
			self::$plugin_path = PD_DISCORD_OAUTH_PLUGIN_DIR_PATH;
			self::$plugin_url = PD_DISCORD_OAUTH_PLUGIN_URL;
			self::$plugin_version = PD_DISCORD_OAUTH_PLUGIN_VERSION;
			self::$authenticated = FALSE;
			
			self::render_admin();
			self::render();
			
        }
		
		/** Rendering admin area **/
		private static function render_admin(){
			
			$plugin_admin = new PD_Discord_OAuth_Admin();
			$prefix = is_network_admin() ? 'network_admin_' : '';
			add_action( 'admin_menu', array( $plugin_admin, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );
			add_action( 'admin_init', array( $plugin_admin, 'update_settings' ) );
			add_action( 'add_meta_boxes', array( $plugin_admin, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $plugin_admin, 'save_post_meta' ) );
			add_action( 'wp_ajax_ban_user', array( $plugin_admin, 'ban_user' ) );
			add_action( 'wp_ajax_force_logout', array( $plugin_admin, 'force_logout' ) );
			add_filter( $prefix . 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $plugin_admin, 'settings_link' ) );
			
		}
		
		/** Rendering front-end **/
		private static function render(){
			
			add_action( 'wp_head', array( __CLASS__, 'set_session' ) );
			add_action( 'init', array( __CLASS__, 'cleanup' ) );
			add_action( 'init', array( __CLASS__, 'is_ajax' ) );
			add_action( 'wp', array( __CLASS__, 'redirect' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
			add_filter( 'the_content', array( __CLASS__, 'login_page' ) );
			add_filter( 'the_content', array( __CLASS__, 'maintainance_page' ) );
			add_filter( 'wp_nav_menu_objects', array( __CLASS__, 'hide_menus' ), 10, 2 );
			add_filter( 'wp_nav_menu_objects', array( __CLASS__, 'remove_menu_items' ), 10, 2 );
			add_filter( 'widget_display_callback', array( __CLASS__, 'hide_widgets' ), 10, 3 );
			add_filter( 'page_template', array( __CLASS__, 'switch_login_template' ) );
			add_filter( 'template_include', array( __CLASS__, 'switch_maintainance_template' ) );
			add_shortcode( 'pd_logout', array( __CLASS__, 'shortcode_logout' ) );
			
		}
		
		/** Setting temporary session **/
		public static function set_session(){
			
			if( ! session_id() )
				session_start();
			
			$_SESSION['post_id'] = get_the_ID();
			
		}
		
		/** Auto cleanup the database sessions **/
		public static function cleanup(){
			
			$user_sessions = maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) ) ? maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) ) : array();
			
			foreach( $user_sessions as $key => $value )
				if( time() > ( $value['session_expiry'] + 60 * 60 * 24 ) &&  $value['session_expiry'] != 0 ) 
					unset( $user_sessions[$key] );
				
			update_option( '_pd_discord_oauth_user_sessions', maybe_serialize( $user_sessions ) );
			
		}

		
		/** If an AJAX request **/
		public static function is_ajax(){
			
			$general_settings = maybe_unserialize( get_option( '_pd_discord_oauth_general_settings' ) );
			$excluded_pages = isset( $general_settings['excluded_pages'] ) ? $general_settings['excluded_pages'] : array();
			$excluded_posts = isset( $general_settings['excluded_posts'] ) ? $general_settings['excluded_posts'] : array();
			$excludes = array_merge( $excluded_pages, $excluded_posts );
			
			if( ! session_id() )
				session_start();
			
			if( ! is_user_logged_in() && defined( 'DOING_AJAX' ) && DOING_AJAX && ! in_array( $_SESSION['post_id'], $excludes ) ){
				
				self::login();
				
				if( ! self::$authenticated )
					wp_die( '<a>' . __( 'You are not logged in', 'pathdigital-discord-oauth' ) . '</a>' );
				
			}
			
		}
		
		/** Redirecting users **/
		public static function redirect(){
			
			if( is_user_logged_in() )
				return;
			
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			$general_settings = maybe_unserialize( get_option( '_pd_discord_oauth_general_settings' ) );
			$exclude_pages = isset( $general_settings['excluded_pages'] ) ? $general_settings['excluded_pages'] : array();
			$exclude_posts = isset( $general_settings['excluded_posts'] ) ? $general_settings['excluded_posts'] : array();
			$excludes = array_merge( $exclude_pages, $exclude_posts );
			
			foreach( array_keys( $excludes, $configuration_settings['login_page'], FALSE ) as $key )
				unset( $excludes[$key] );
				
			if( ! isset( $configuration_settings['login_page'] ) || ( is_page( $configuration_settings['login_page'] ) && isset( $_GET['pd_oauth_error'] ) ) )
				return;
			
			self::login();
			
			if( ( is_page() || is_single() ) && in_array( get_the_ID(), $excludes ) )
				return;
			
			
			if( self::$authenticated ){
				
				if( is_page( $configuration_settings['login_page'] ) ){
					
					if( isset( $general_settings['success_redirect_page'] ) && ! empty( $general_settings['success_redirect_page'] ) && get_post_status( intval( $general_settings['success_redirect_page'] ) ) === 'publish' )
						wp_redirect( get_permalink( $general_settings['success_redirect_page'] ) );
					else
						wp_redirect( get_site_url() );
					
				} else {
					
					$page_blocked = self::post_blocked( get_the_ID() );
					
					if( $page_blocked )
						wp_redirect( get_permalink( $configuration_settings['login_page'] ) . '?pd_oauth_error=5&ref=' . get_the_ID() );
					
					
				}
				
			}else{
				
				if( ! is_page( $configuration_settings['login_page'] ) )
					wp_redirect( get_permalink( $configuration_settings['login_page'] ) );
				
			}
			
		}
		
		/** Log in the user **/
		public static function login(){
			
			$discord = new PD_Discord_OAuth_Dicord();

			$discord->login();
			$is_authenticated = $discord->is_authenticated();
			
			if( $is_authenticated )
				self::$authenticated = TRUE;
			
		}
		
		/** Registering styles for the front end **/
		public static function enqueue_styles(){
			
			wp_enqueue_style( 'pathdigital-discord-oauth',
				self::$plugin_url . '/css/style.css', array(),
				self::$plugin_version
			);
			
		}
		
		/** Registering scripts for the front end **/
		public static function enqueue_scripts(){
			
			wp_enqueue_script( 'pathdigital-discord-oauth',
				self::$plugin_url . '/js/script.js',
				array( 'jquery' ),
				self::$plugin_version,
				true
			);
			
			wp_localize_script( 'pathdigital-discord-oauth',
				'pathdigital_discord_oauth_ajax',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'plugin_url' => self::$plugin_url )
			);
			
		}
		
		/** Login page **/
		public static function login_page( $content ){
			
			$out = $error = '';
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			
			if( ! isset( $configuration_settings['login_page'] ) || ! is_page( $configuration_settings['login_page'] ) )
				return $content;
			
			$general_settings = maybe_unserialize( get_option( '_pd_discord_oauth_general_settings' ) );
			$login_button_styles = isset( $general_settings['login_button_styles'] ) ? implode( '; ', $general_settings['login_button_styles'] ) : 'background: #5865f2; border-color: #5865f2; color: #fff; border-radius: 5px; text-decoration: none; font-weight: 500';
			
			$state = wp_create_nonce();
			
			$params = array(
				'client_id' => $configuration_settings['client_id'],
				'redirect_uri' => get_permalink( $configuration_settings['login_page'] ),
				'response_type' => 'code',
				'scope' => $configuration_settings['scope'],
				'state' => $state
			);
			
			$login_page_settings = maybe_unserialize( get_option( '_pd_discord_oauth_login_page_settings' ) );
			
			if( isset( $_GET['pd_oauth_error'] ) ){
				
				$error_message = __( 'Your account couldn\'t be validated.' );
				
				if( isset( $login_page_settings['not_in_server_error'] ) && $login_page_settings['not_in_server_error'] != '' && $_GET['pd_oauth_error'] == 3 )
					$error_message = wp_unslash( $login_page_settings['not_in_server_error'] );
				
				if( isset( $login_page_settings['no_role_error'] ) && $login_page_settings['no_role_error'] != '' && $_GET['pd_oauth_error'] == 4 )
					$error_message = wp_unslash( $login_page_settings['no_role_error'] );
				
				if( isset( $login_page_settings['banned_error'] ) && $login_page_settings['banned_error'] != '' && $_GET['pd_oauth_error'] == 5 )
					$error_message = wp_unslash( $login_page_settings['banned_error'] );
				
				$error = '<p class="pd-error"><svg height="32" style="overflow:visible;enable-background:new 0 0 32 32" viewBox="0 0 32 32" width="32" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g id="Error_1_"><g id="Error"><circle cx="16" cy="16" id="BG" r="16" style="fill:#f44336;"/><path d="M14.5,25h3v-3h-3V25z M14.5,6v13h3V6H14.5z" id="Exclamatory_x5F_Sign" style="fill:#ffffff;"/></g></g></g></svg>' . $error_message . '</p>';
				
				$bot_settings = maybe_unserialize( get_option( '_pd_discord_bot_configuration_settings' ) );
				
				/** Call the webhook **/
				if( isset( $bot_settings['webhook'] ) && $bot_settings['webhook'] != '' ){
					
					$bot_icon = isset( $bot_settings['bot_icon'] ) && esc_url( $bot_settings['bot_icon'] ) != '' ? $bot_settings['bot_icon'] : PD_DISCORD_OAUTH_PLUGIN_URL . '/images/icon_128x128.png';
					$embed_color = '#ff0000';
					$user_sessions = maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) );
					$me = isset( $_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH] ) && isset( $user_sessions[$_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH]] ) ? $user_sessions[$_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH]] : array();
					
					$webhook_data = array(
						'embeds' => array (
							array(
								'description' => '**' . ( isset( $me['user']['username'] ) ? $me['user']['username'] : '' ) . ( isset( $me['user']['discriminator'] ) ? '#' . @$me['user']['discriminator'] : '' ) . ' failed to login**',
								'color' => hexdec( str_replace( '#', '', $embed_color) ),
								'footer' => array(
									'text' => get_bloginfo('name'),
									'icon_url' => $bot_icon
								),
								'fields' => array(
									array(
										'name' => 'User Id:',
										'value' => isset( $me['user']['id'] ) ? $me['user']['id'] : ''
									),
									array(
										'name' => 'Username:',
										'value' => ( isset( $me['user']['username'] ) ? $me['user']['username'] : '' ) . ( isset( $me['user']['discriminator'] ) ? '#' . @$me['user']['discriminator'] : '' )
									),
									array(
										'name' => 'Time:',
										'value' => date( 'jS M Y h:i:s A (T)', time() )
									),
									array(
										'name' => 'Error:',
										'value' => $error_message
									)
								)
							)
						)
					);
					
					$headers = array(
						'Content-Type' => 'application/json'
					);
					
					wp_remote_post( $bot_settings['webhook'], array(
						'method' => 'POST',
						'timeout' => 45,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking' => true,
						'headers' => $headers,
						'body' => json_encode( $webhook_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
						'cookies' => array(),
						'sslverify' => false
					) );
					
				}
			
			}
			
			$html = '<div class="pd-login-button-wrap">';
				$html .= '<div class="wp-block-button">';
					$html .= '<a href="' . ( ! is_user_logged_in() ? esc_url( $configuration_settings['base_authorization_url'] ) . '?' . http_build_query( $params ) : '#' ) . '" class="wp-block-button__link button"' . ( $login_button_styles != '' ? ' style="' . $login_button_styles . '"' : '' ) . '>' . __( 'Continue with Discord', 'pathdigital-discord-oauth' ) . '</a>';
				$html .= '</div>';
			$html .= '</div>';
			
			if( is_user_logged_in() )
				$html .= '<p style="text-align: center;"><font color="red" size="2">Please log out to check the button functionality.</font></p>';
			
			$out = $error . $content . $html;
			
			$login_page = isset( $login_page_settings['login_page'] ) ? $login_page_settings['login_page'] : true;
			$logo = isset( $login_page_settings['logo'] ) && esc_url( $login_page_settings['logo'] ) != '' ? esc_url( $login_page_settings['logo'] ) : PD_DISCORD_OAUTH_PLUGIN_URL . '/images/icon_256x256.png';
			$heading = isset( $login_page_settings['heading'] ) && esc_html( $login_page_settings['heading'] ) != '' ? esc_html( $login_page_settings['heading'] ) : get_bloginfo( 'name' );
			$subtext = isset( $login_page_settings['subtext'] ) && esc_html( $login_page_settings['subtext'] ) != '' ? esc_html( $login_page_settings['subtext'] ) : __( 'Please sign in with Discord to continue to the website.', 'pathdigital-discord-oauth' );
			$background_color = isset( $login_page_settings['background_color'] ) && esc_html( $login_page_settings['background_color'] ) != '' ? esc_html( $login_page_settings['background_color'] ) : '#C9CDFB';
			$background_image = isset( $login_page_settings['background_image'] ) && esc_url( $login_page_settings['background_image'] ) != '' ? esc_url( $login_page_settings['background_image'] ) : false;
			
			if( $login_page ){
				
				$out = '<div class="pd-login-box-wrap">';
					$out .= '<div class="pd-login-logo-wrap">';
						$out .= '<img src="'. $logo .'" alt="">';
					$out .= '</div>';
					$out .= '<h3 class="pd-login-heading">' . $heading . '</h3>';
					$out .= '<p class="pd-login-subtext">' . $subtext . '</p>';
					$out .= $error . $html;
				$out .= '</div>';
				$out .= '<div class="pd-login-background"' . ( $background_color || $background_image ? 'style="' . ( $background_color ? 'background-color: ' . $background_color . ';' : '' ) . ( $background_image ? 'background-image: url('. $background_image . ');' : '' ) . '"' : '' ). '></div>';
				
			}
			
			return $out;
			
		}
		
		/** Switch to login template **/
		public static function switch_login_template( $template ){
			
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			$login_page_settings = maybe_unserialize( get_option( '_pd_discord_oauth_login_page_settings' ) );
			$login_page = isset( $login_page_settings['login_page'] ) ? $login_page_settings['login_page'] : true;
			
			if( $configuration_settings['login_page'] == get_queried_object_id() && $login_page )
				$template = PD_DISCORD_OAUTH_PLUGIN_DIR_PATH . 'templates/login.php';
			
			return $template;
		
		}
		
		/** Maintainance page **/
		public static function maintainance_page( $content ){
			
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			
			if( $configuration_settings['login_page'] == get_queried_object_id() )
				return $content;
			
			$maintainance_page_settings = maybe_unserialize( get_option( '_pd_discord_oauth_maintainance_page_settings' ) );
			$maintainance_mode = isset( $maintainance_page_settings['maintainance_mode'] ) ? $maintainance_page_settings['maintainance_mode'] : false;
			$logo = isset( $maintainance_page_settings['logo'] ) && esc_url( $maintainance_page_settings['logo'] ) != '' ? esc_url( $maintainance_page_settings['logo'] ) : PD_DISCORD_OAUTH_PLUGIN_URL . '/images/icon_256x256.png';
			$heading = isset( $maintainance_page_settings['heading'] ) && esc_html( $maintainance_page_settings['heading'] ) != '' ? esc_html( $maintainance_page_settings['heading'] ) : __( 'We’ll be back soon!', 'pathdigital-discord-oauth' );
			$subtext = isset( $maintainance_page_settings['subtext'] ) && esc_html( $maintainance_page_settings['subtext'] ) != '' ? esc_html( $maintainance_page_settings['subtext'] ) : __( 'Sorry for the inconvenience. We’re performing some maintenance at the moment. If you need to you can always follow us on Twitter for updates, otherwise we’ll be back up shortly!', 'pathdigital-discord-oauth' );
			$background_color = isset( $maintainance_page_settings['background_color'] ) && esc_html( $maintainance_page_settings['background_color'] ) != '' ? esc_html( $maintainance_page_settings['background_color'] ) : '#C9CDFB';
			$background_image = isset( $maintainance_page_settings['background_image'] ) && esc_url( $maintainance_page_settings['background_image'] ) != '' ? esc_url( $maintainance_page_settings['background_image'] ) : false;
			
			if( $maintainance_mode && ! is_user_logged_in() ){
				$out = '<div class="pd-maintainance-box-wrap">';
					$out .= '<div class="pd-maintainance-logo-wrap">';
						$out .= '<img src="'. $logo .'" alt="">';
					$out .= '</div>';
					$out .= '<h3 class="pd-maintainance-heading">' . $heading . '</h3>';
					$out .= '<p class="pd-maintainance-subtext">' . $subtext . '</p>';
				$out .= '</div>';
				$out .= '<div class="pd-maintainance-background"' . ( $background_color || $background_image ? 'style="' . ( $background_color ? 'background-color: ' . $background_color . ';' : '' ) . ( $background_image ? 'background-image: url('. $background_image . ');' : '' ) . '"' : '' ). '></div>';
				
				return $out;
				
			}
			
			return $content;
			
		}
		
		/** Switch to maintainance template **/
		public static function switch_maintainance_template( $template ){
			
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			
			if( $configuration_settings['login_page'] == get_queried_object_id() )
				return $template;
			
			$maintainance_page_settings = maybe_unserialize( get_option( '_pd_discord_oauth_maintainance_page_settings' ) );
			$maintainance_mode = isset( $maintainance_page_settings['maintainance_mode'] ) ? $maintainance_page_settings['maintainance_mode'] : false;
			
			if( $maintainance_mode && ! is_user_logged_in() )
				$template = PD_DISCORD_OAUTH_PLUGIN_DIR_PATH . 'templates/maintainance.php';
			
			return $template;
		
		}
		
		/** Hiding menus **/
		public static function hide_menus( $menu_objects, $args ){
			
			$general_settings = maybe_unserialize( get_option( '_pd_discord_oauth_general_settings' ) );
			$hide_menus = $general_settings['hide_menus'];
			
			if( $hide_menus && ! self::$authenticated && ! is_user_logged_in() ){
			
				foreach ( $menu_objects as $key => $menu_object )
					unset(  $menu_objects[$key] );
				
			}
			
			return $menu_objects;
			
		}
				
		/** Hiding widgets **/
		public static function hide_widgets( $instance, $widget, $args ){
			
			$general_settings = maybe_unserialize( get_option( '_pd_discord_oauth_general_settings' ) );
			$hide_widgets = $general_settings['hide_widgets'];
			
			if( $hide_widgets && ! self::$authenticated && ! is_user_logged_in() )
				return false;
			else
				return $instance;
			
		}
		
		/** Removing menu items **/
		public static function remove_menu_items( $menu_objects, $args ){
			
			foreach ( $menu_objects as $key => $menu_object ){
				$object_id = $menu_object->object_id;
				$object = $menu_object->object;
				
				if( $object === 'page' && self::post_blocked( $object_id ) )
					unset(  $menu_objects[$key] );
				
			}
			
			return $menu_objects;
			
		}
		
		/** Log out shortcode **/
		static function shortcode_logout( $atts ){
			
			$default = array(
				'label' => 'Logout',
				'button' => 0
			);
			
			$a = shortcode_atts( $default, $atts );
			
			return '<a href="'. get_site_url() . ( parse_url( get_site_url(), PHP_URL_QUERY ) ? '&' : '?' ) . 'pd_discord_oauth_logout=1"' . ( $a['button'] ? ' class="button btn wp-block-button__link"' : '' ) . '>' . $a['label'] . '</a>';
			
		}
		
		/** Is post blocked **/
		static function post_blocked( $page_id ){
			
			if( is_user_logged_in() )
				return;
			
			$page_blocked = true;
			$selected_roles = get_post_meta( $page_id, '_pd_page_accessible_roles', true ) ? get_post_meta( $page_id, '_pd_page_accessible_roles', true ) : array();
			
			if( ! $selected_roles )
				return;
			
			$all_server_roles = maybe_unserialize( get_option( '_pd_discord_all_server_roles' ) );
			$user_sessions = maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) );
			$me = isset( $_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH] ) && isset( $user_sessions[$_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH]] ) ? $user_sessions[$_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH]] : array();
			$allowed_roles = $server_roles = array();
			
			if( $all_server_roles ){
				foreach( $all_server_roles as $server ){
					
					if( isset( $me['user_roles'][$server['id']] ) ){
						
						usort( $server['roles'], function( $item1, $item2 ){
							return $item2['position'] <=> $item1['position'];
						} );
						
						foreach( $server['roles'] as $role ){
							
							if( $server['id'] != $role['id'] )
								$server_roles[$server['id']][] = $role['id'];
							
						}
						
					}
					
				}	
			}
			
			foreach( $server_roles as $server => $server_user_roles ){
				if( isset( $selected_roles[$server] ) ){
					
					foreach( $server_user_roles as $server_user_role ){
						
						$allowed_roles[$server][] = $server_user_role;
						
						if( $server_user_role == $selected_roles[$server][0] )
							break;
					}
				}
			}
			
			foreach( $selected_roles as $server => $selected_roles ){
				foreach( $selected_roles as $selected_role )
					if( $server == $selected_role )
						$allowed_roles[$server][] = $selected_role;
			}
			
			foreach( $allowed_roles as $k => $v )
				if( ! empty( array_intersect( $me['user_roles'][$k], $v ) ) )
					$page_blocked = false;
				
			return $page_blocked;			
			
		}
		
	}
}

register_activation_hook( __FILE__, array( 'PD_Discord_OAuth', 'activation' ) );
add_action( 'plugins_loaded', array( 'PD_Discord_OAuth', 'run' ) );
?>