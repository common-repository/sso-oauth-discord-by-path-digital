<?php

if( ! function_exists( 'add_action' ) )
	exit;

if( ! class_exists( 'PD_Discord_OAuth_Dicord' ) ){
	
	class PD_Discord_OAuth_Dicord{
		
		protected static $id;
		protected static $username;
		protected static $avatar;
		protected static $discriminator;
		protected static $authenticated;
		protected static $server_validation;
		protected static $user_has_role;
		protected static $user_not_blocked;
		protected static $user_in_whitelisted_server;
		protected static $user_in_blacklisted_server;
		
		public function __construct(){
			
			self::$id = NULL;
			self::$username = NULL;
			self::$avatar = NULL;
			self::$discriminator = NULL;
			self::$authenticated = FALSE;
			self::$server_validation = FALSE;
			self::$user_has_role = FALSE;
			self::$user_not_blocked = FALSE;
			self::$user_in_whitelisted_server = FALSE;
			self::$user_in_blacklisted_server = FALSE;
			
        }
		
		/** Login **/
		public static function login(){
			
			$user_sessions = maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) );
			
			if( isset( $_GET['pd_discord_oauth_logout'] ) && $_GET['pd_discord_oauth_logout'] ){
				
				unset( $user_sessions[$_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH]] );
				update_option( '_pd_discord_oauth_user_sessions', maybe_serialize( $user_sessions ) );
				
				unset( $_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH] ); 
				setcookie( 'PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH, NULL, -1, COOKIEPATH, COOKIE_DOMAIN );
				
			}
			
			
			$session = self::get_user_session();
			
			if( ! empty( $session ) && version_compare( $session['plugin_version'], '1.6.0' ) >= 0 ){
				
				$me = $session['user'];
				
				/** If the user in server and has roles **/
				if( self::server_validation() && self::user_has_role( $me ) && self::user_not_blocked( $me ) )
					self::$authenticated = TRUE;
				
			}else{
				
				self::authenticate();
				
			}
			
		}
		
		/** Authenticate user with discord **/
		public static function authenticate(){
					
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			$bot_settings = maybe_unserialize( get_option( '_pd_discord_bot_configuration_settings' ) );
			$general_settings = maybe_unserialize( get_option( '_pd_discord_oauth_general_settings' ) );
			$user_sessions = maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) ) ? maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) ) : array();
			
			if( ( isset( $_GET['code'] ) && ! empty( $_GET['code'] ) ) && wp_verify_nonce( sanitize_text_field( $_GET['state'] ) ) ){
				
				/** Exchange the auth code for a token **/
				$api_response = self::api_request( $configuration_settings['token_url'], $configuration_settings['client_id'], $configuration_settings['client_secret'], array (
					'grant_type' => 'authorization_code',
					'client_id' => $configuration_settings['client_id'],
					'client_secret' => $configuration_settings['client_secret'],
					'redirect_uri' => get_permalink( $configuration_settings['login_page'] ),
					'code' => sanitize_text_field( $_GET['code'] )
				), TRUE );
				
				if( ! isset( $api_response->errors ) && $api_response['response']['code'] === 200 ){
					
					$body = json_decode( $api_response['body'], true );
					
					if( isset( $body['access_token'] ) ){
						
						$key = self::generate_id();
						
						$time = time();
						$session_duration = isset( $general_settings['session_expiry'] ) ? ( int ) $general_settings['session_expiry'] : 3;
						$session_expiry = $session_duration > 0 ? ( $time + $session_duration * 60 * 60 * 24 ) : 0;
						$session_data = array (
							'token' => $body['access_token'],
							'plugin_version' => PD_DISCORD_OAUTH_PLUGIN_VERSION,
							'session_expiry' => $session_expiry,
							'created' => $time
						);
						
						$user_sessions[$key] = $session_data;
						update_option( '_pd_discord_oauth_user_sessions', maybe_serialize( $user_sessions ) );
						
						setcookie( 'PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH, $key, $session_expiry, COOKIEPATH, COOKIE_DOMAIN );
						$_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH] = $key;
						
						$me = self::get_user_info();
						
						$new_session_data = array(
							'user' => $me
						);
						
						self::update_session( $new_session_data );
						
						/** If the user in server and has roles and not blocked **/
						if( self::server_validation() && self::user_has_role( $me ) && self::user_not_blocked( $me ) ){
							
							self::$authenticated = TRUE;
							
							/** Call the webhook **/
							if( isset( $bot_settings['webhook'] ) && $bot_settings['webhook'] != '' ){
								
								$bot_icon = isset( $bot_settings['bot_icon'] ) && esc_url( $bot_settings['bot_icon'] ) != '' ? $bot_settings['bot_icon'] : PD_DISCORD_OAUTH_PLUGIN_URL . '/images/icon_128x128.png';
								$embed_color = isset( $bot_settings['embed_color'] ) && esc_html( $bot_settings['bot_icon'] ) !='' ? $bot_settings['embed_color'] : '#5865f2';
								
								$webhook_data = array(
									'embeds' => array (
										array(
											'description' => '**' . ( isset( $me['username'] ) ? $me['username'] : '' ) . ( isset( $me['discriminator'] ) ? '#' . @$me['discriminator'] : '' ) . ' has signed up**',
											'color' => hexdec( str_replace( '#', '', $embed_color) ),
											'footer' => array(
												'text' => get_bloginfo('name'),
												'icon_url' => $bot_icon
											),
											'fields' => array(
												array(
													'name' => 'User Id:',
													'value' => isset( $me['id'] ) ? $me['id'] : ''
												),
												array(
													'name' => 'Username:',
													'value' => ( isset( $me['username'] ) ? $me['username'] : '' ) . ( isset( $me['discriminator'] ) ? '#' . @$me['discriminator'] : '' )
												),
												array(
													'name' => 'Logged In:',
													'value' => date( 'jS M Y h:i:s A (T)', $time ),
													'inline' => TRUE
												),
												array(
													'name' => 'Expiry:',
													'value' => date( 'jS M Y h:i:s A (T)', $session_expiry ),
													'inline' => TRUE
												)
											)
										)
									)
								);
								if( self::$user_in_whitelisted_server )
									$webhook_data['embeds'][0]['fields'][] = array( 'name' => 'Whitelisted:', 'value' => 'True' );
								
								$headers = array(
									'Content-Type' => 'application/json'
								);
								
								$api_response = self::api_request( $bot_settings['webhook'], null, null, json_encode( $webhook_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), TRUE, $headers );
								
							}
							
						}
						
					}else{
						
						wp_redirect( get_permalink( $configuration_settings['login_page'] ) . '?pd_oauth_error=2' );
						
					}
					
				}else{
					
					wp_redirect( get_permalink( $configuration_settings['login_page'] ) . '?pd_oauth_error=1' );
					
				}
				
			}
			
		}
		
		/** Check if the user in correct server **/
		public static function server_validation(){
			
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			$server_validation = isset( $configuration_settings['server_validation'] ) ? $configuration_settings['server_validation'] : FALSE;
			$servers = isset( $configuration_settings['server_ids'] ) ? $configuration_settings['server_ids'] : array();
			$servers_blocked = isset( $configuration_settings['server_ids_blocked'] ) ? $configuration_settings['server_ids_blocked'] : array();
			
			if( ! $server_validation || ( empty( $servers ) && empty( $servers_blocked ) ) )
				return TRUE;
			
			$time = time();
			$expiry = $time - 60 * 60;
			$session = self::get_user_session();
			$guilds = isset( $session['servers'] ) && $session['created'] > $expiry ? $session['servers'] : self::get_user_guilds();
			$user_servers = array();
			
			foreach ( $guilds as $guild ){
				
				if( isset( $guild['id'] ) ){
					
					$user_servers[] = array(
						'id' => $guild['id'],
						'name' => $guild['name']
					);
					
					if( in_array( $guild['id'], $servers ) )
						self::$user_in_whitelisted_server = TRUE;
					
					if( in_array( $guild['id'], $servers_blocked ) )
						self::$user_in_blacklisted_server = TRUE;
					
				}
			
			}
			
			$new_session_data = array(
				'servers' => $user_servers,
				'created' => $session['created'] > $expiry ? $session['created'] : $time
			);
			
			self::update_session( $new_session_data );
			
			if( self::$user_in_whitelisted_server && ! self::$user_in_blacklisted_server ){
				
				self::$server_validation = TRUE;
				return TRUE;
				
			}
			
			wp_redirect( get_permalink( $configuration_settings['login_page'] ) . '?pd_oauth_error=3' );
			
		}
		
		/** Check if the user has correct roles **/
		public static function user_has_role( $me = array() ){
			
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_bot_configuration_settings' ) );
			$role_validation = $configuration_settings['role_validation'];
			$validation_condition = $configuration_settings['validation_condition'];
			$minimum_roles = ! empty( $configuration_settings['server_roles'] ) ? $configuration_settings['server_roles'] : array();
			$allowed_roles = ! empty( $configuration_settings['allowed_roles'] ) ? $configuration_settings['allowed_roles'] : array();
			$servers = array();
			
			foreach( $minimum_roles as $server => $role )
				$servers[] = $server;
			
			if( ! $role_validation || empty( $servers ) )
				return TRUE;
			
			
			$time = time();
			$expiry = $time - 60 * 60;
			$session = self::get_user_session();
			$user_roles = isset( $session['user_roles'] ) && $session['created'] > $expiry ? $session['user_roles'] : self::get_user_roles( $servers, $me );
			$server_roles = isset( $session['server_roles'] ) && $session['created'] > $expiry ? $session['server_roles'] : self::get_server_roles( $servers );
			
			$new_session_data = array(
				'user_roles' => $user_roles,
				'server_roles' => $server_roles,
				'created' => $session['created'] > $expiry ? $session['created'] : $time
			);
			
			self::update_session( $new_session_data );
			
			$granted_roles = self::get_granted_roles( $minimum_roles, $server_roles, $allowed_roles );
			$found_in_servers = 0;
			
			foreach( $servers as $server ){
				
				$found_in_server  = FALSE;
				
				if( isset( $user_roles[$server] ) )
					foreach( $user_roles[$server] as $user_role )
						if( in_array( $user_role, $granted_roles[$server] ) )
							$found_in_server  = TRUE;
				
				if( $found_in_server )
					$found_in_servers++;
				
			}
			
			if( ( $validation_condition == 'one' && $found_in_servers > 0 ) || ( $validation_condition == 'all' && $found_in_servers == count( $servers ) ) ){
				
				self::$user_has_role = TRUE;
				return TRUE;
				
			}
			
			$oauth_configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			wp_redirect( get_permalink( $oauth_configuration_settings['login_page'] ) . '?pd_oauth_error=4' );
			
		}
		
		/** Check if the user is blocked **/
		public static function user_not_blocked( $me ){
			
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			$user_validation = isset( $configuration_settings['user_validation'] ) ? $configuration_settings['user_validation'] : FALSE;
			$users = isset( $configuration_settings['user_ids'] ) ? $configuration_settings['user_ids'] : array();
			
			if( ! $user_validation || empty( $users ) )
				return TRUE;
			
			if( ! in_array( $me['id'], $users ) ){
				
				self::$user_not_blocked = TRUE;
				return TRUE;
				
			}
			
			wp_redirect( get_permalink( $configuration_settings['login_page'] ) . '?pd_oauth_error=5' );
			
		}
		
		/** Get user guilds **/
		public static function get_user_guilds(){
			
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			$api_response = self::api_request( $configuration_settings['user_guilds_endpoint'] );
			$guilds = ( ! isset( $api_response->errors ) && $api_response['response']['code'] === 200 ) ? json_decode( $api_response['body'], true ) : array();
			
			return $guilds;
			
		}
		
		/** Get server roles **/
		public static function get_server_roles( $servers ){
			
			$server_roles = array();
			$bot_configuration_settings = maybe_unserialize( get_option( '_pd_discord_bot_configuration_settings' ) );
			
			$headers = array(
				'Accept'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Bot ' . $bot_configuration_settings['bot_token'],
				'Content-Type' => 'application/x-www-form-urlencoded',
			);
			
			foreach( $servers as $server ){
				
				$endpoint = 'https://discordapp.com/api/guilds/' . $server;
				
				$response = wp_remote_get( $endpoint, array(
					'method' => 'GET',
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => $headers,
					'cookies' => array(),
					'sslverify' => false
				) );
				
				if( $response['response']['code'] === 200 ){
					
					$body = json_decode( $response['body'], true );
					$roles = $body['roles'];
					
					usort( $roles, function ($item1, $item2){
						return $item1['position'] <=> $item2['position'];
					} );
					
					foreach( $roles as $role )
						$server_roles[$server][] = $role['id'];
						
				}
				
			}
			
			return $server_roles;
			
		}
		
		/** Get user roles **/
		public static function get_user_roles( $servers, $me ){
			
			$user_roles = array();
			$bot_configuration_settings = maybe_unserialize( get_option( '_pd_discord_bot_configuration_settings' ) );
			
			if( empty( $me ) )
				return $user_roles;
			
			$headers = array(
				'Accept'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Bot ' . $bot_configuration_settings['bot_token'],
				'Content-Type' => 'application/x-www-form-urlencoded',
			);
			
			foreach( $servers as $server ){
				
				$endpoint = 'https://discordapp.com/api/guilds/' . $server . '/members/' . $me['id'];
				
				$response = wp_remote_get( $endpoint, array(
					'method' => 'GET',
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => $headers,
					'cookies' => array(),
					'sslverify' => false
				) );
				
				if( $response['response']['code'] === 200 ){
					
					$body = json_decode( $response['body'], true );
					$roles = $body['roles'];
					
					array_push( $roles, ( string ) $server );
					
					foreach( $roles as $role )
						$user_roles[$server][] = $role;
						
				}
				
			}
			
			return $user_roles;
			
		}
		
		
		
		/** Get granted roles **/
		public static function get_granted_roles( $minimum_roles, $all_roles, $allowed_roles = array() ){
			
			$granted_roles = array();
			
			foreach( $minimum_roles as $server => $minimum_role ){
				
				if( isset( $all_roles[$server] ) ){
					
					$roles_order_desc = array_reverse( $all_roles[$server] );
					
					foreach( $roles_order_desc as $role ){
						
						$granted_roles[$server][] = $role;
						
						if( $minimum_role == $role )
							break;
							
						
					}
					
				}
				
			}
			
			foreach( $allowed_roles as $server => $allowed_role ){
				
				if( isset( $all_roles[$server] ) ){
					
					foreach( $all_roles[$server] as $role ){
						
						if( in_array( $role, $allowed_role ) && ! in_array( $role, $granted_roles[$server] ) )
							$granted_roles[$server][] = $role;
							
						
					}
					
				}
				
			}
			
			return $granted_roles;
			
		}
		
		/** Get user info **/
		public static function get_user_info(){
			
			$me = array();
			
			$configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
			$api_response = self::api_request( $configuration_settings['user_info_endpoint'] );
			
			if( $api_response['response']['code'] === 200 )
				$me = json_decode( $api_response['body'], true );
			
			return $me;
			
		}
		
		/** Update the session **/
		public static function update_session( $session_data ){
			
			if( ! isset( $_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH] ) && empty( self::get_user_session() ) )
				return FALSE;
			
			/** Old sessions **/
			$user_sessions = maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) );
			
			/** User session **/
			$session = self::get_user_session();
			
			foreach( $session_data as $key => $value )
				$session[$key] = $value;
			
			/** New sessions */
			$user_sessions[$_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH]] = $session;
			update_option( '_pd_discord_oauth_user_sessions', maybe_serialize( $user_sessions ) );
			
		}
		
		/** Get the user session **/
		public static function get_user_session(){
			
			$user_sessions = maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) );
			
			return isset( $_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH] ) && isset( $user_sessions[$_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH]] ) ? $user_sessions[$_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH]] : array();
			
		}
		
		/** Call the API **/
		private static function api_request( $endpoint, $client_id = NULL, $client_secret = NULL, $body = NULL, $post = FALSE, $headers = array() ){
			
			$user_sessions = maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) );
			
			if( empty( $headers ) )
				$headers = array(
					'Accept'  => 'application/json',
					'charset'       => 'UTF - 8',
					'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $client_secret ),
					'Content-Type' => 'application/x-www-form-urlencoded',
				);
			
			if( isset( $_COOKIE['PD_DISCORD_OAUTH_' . PD_DISCORD_OAUTH_HASH] ) && ! $post ){
				
				$session = self::get_user_session();
				
				$headers['Authorization'] = 'Bearer ' . $session['token'];
				
				$response = wp_remote_get( $endpoint, array(
					'method' => 'GET',
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => $headers,
					'cookies' => array(),
					'sslverify' => false
				) );
				
			}else{
				
				$response = wp_remote_post( $endpoint, array(
					'method' => 'POST',
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => $headers,
					'body' => $body,
					'cookies' => array(),
					'sslverify' => false
				) );
				
			}
			
			return $response;
			
		}
		
		/** Generate an id **/
		public static function generate_id(){
			
			return md5( sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x-%s',
							mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
							mt_rand( 0, 0xffff ),
							mt_rand( 0, 0x0C2f ) | 0x4000,
							mt_rand( 0, 0x3fff ) | 0x8000,
							mt_rand( 0, 0x2Aff ), mt_rand( 0, 0xffD3 ), mt_rand( 0, 0xff4B ),
							time()
						) );
			
		}
		
		/** Is authenticated **/
		public static function is_authenticated(){
			
			return self::$authenticated;
			
		}
		
		/** Is user in server **/
		private static function is_server_validation(){
			
			return self::$server_validation;
			
		}
		
	}

}

?>