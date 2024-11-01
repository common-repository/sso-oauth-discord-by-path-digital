<?php

if( ! function_exists( 'add_action' ) )
	exit;

if( ! function_exists( 'pd_discord_oauth_show_admin_notice' ) ){

	function pd_discord_oauth_show_admin_notice( $status ){
		
		$notice = new PD_Discord_OAuth_Admin_Notice;
		$notice->show_notice( $status );
		
	}

}

if( ! class_exists( 'PD_Discord_OAuth_Admin_Notice' ) ){
	
	class PD_Discord_OAuth_Admin_Notice{
		
		/** Showing the notice **/
		public function show_notice( $status ){
			
			if( $status == 'success' ){
				
				remove_action( 'admin_notices', array( $this, 'error_notice') );
				add_action( 'admin_notices', array( $this, 'success_notice' ) );
				
			}else if( $status == 'error' ){
				
				remove_action( 'admin_notices', array( $this, 'success_notice') );
				add_action( 'admin_notices', array( $this, 'error_notice') );
				
			}
			
		}
		
		/** Success notice **/
		public function success_notice(){
			?>
			
			<div class="updated">
				<p><strong><?php echo get_option( '_pd_discord_oauth_notice' ); ?></strong></p>
			</div>
			
			<?php
		}
		
		/** Error notice **/
		public function error_notice(){
			?>
			
			<div class="error">
				<p><strong><?php echo get_option( '_pd_discord_oauth_notice' ); ?></strong></p>
			</div>
			
			<?php
		}
		
	}
	
}

?>