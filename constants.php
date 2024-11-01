<?php

if( ! function_exists( 'add_action' ) )
	exit;

define( 'PD_DISCORD_OAUTH_PLUGIN_NAME', 'SSO OAuth for Discord by path digital' );
define( 'PD_DISCORD_OAUTH_PLUGIN_SLUG', 'sso-oauth-discord-by-path-digital' );
define( 'PD_DISCORD_OAUTH_PLUGIN_VERSION', '3.1.3' );
define( 'PD_DISCORD_OAUTH_PLUGIN_URL', plugins_url( PD_DISCORD_OAUTH_PLUGIN_SLUG ) );
define( 'PD_DISCORD_OAUTH_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'PD_DISCORD_OAUTH_HASH', md5( get_site_url() ) );

?>