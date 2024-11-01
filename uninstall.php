<?php

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

delete_option( '_pd_discord_oauth_configuration_settings' );
delete_option( '_pd_discord_bot_configuration_settings' );
delete_option( '_pd_discord_oauth_general_settings' );
delete_option( '_pd_discord_oauth_notice' );
delete_option( '_pd_discord_oauth_user_sessions' );
delete_option( '_pd_discord_all_server_roles' );
delete_option( '_pd_discord_oauth_login_page_settings' );
delete_option( '_pd_discord_oauth_maintainance_page_settings' );

?>