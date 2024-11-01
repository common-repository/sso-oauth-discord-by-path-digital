<?php

if( ! function_exists( 'add_action' ) )
	exit;

require( 'class-admin-notice.php' );

if( ! class_exists( 'PD_Discord_OAuth_Admin_Menu' ) ){
	
	class PD_Discord_OAuth_Admin_Menu{
		
		/** Rendering menu **/
		public static function render_menu(){
			
			$current_tab = isset( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : '';
			?>
			<div class="wrap">
				<div>
					<img style="float: left; padding: 8px;" src="<?php echo plugin_dir_url( __FILE__ ); ?>/images/icon_32x32.png">
				</div>
			</div>
		    <div class="wrap">
				<h1><?php esc_html_e( 'SSO OAuth for Discord by path digital', 'pathdigital-discord-oauth' ); ?></h1>
			</div>
			<div id="tab">
				<h2 class="nav-tab-wrapper">
					<a id="tab-oauth2" class="nav-tab <?php if( $current_tab == 'oauth2' ) echo 'nav-tab-active'; ?>" href="admin.php?page=pd_discord_oauth_settings&tab=oauth2"><?php esc_html_e( 'OAuth2 Settings', 'pathdigital-discord-oauth' ); ?></a>
					<a id="tab-bot" class="nav-tab <?php if( $current_tab == 'bot' ) echo 'nav-tab-active'; ?>" href="admin.php?page=pd_discord_oauth_settings&tab=bot"><?php esc_html_e( 'Bot Settings', 'pathdigital-discord-oauth' ); ?></a>
					<a id="tab-general" class="nav-tab <?php if( $current_tab == 'general' ) echo 'nav-tab-active'; ?>" href="admin.php?page=pd_discord_oauth_settings&tab=general"><?php esc_html_e( 'General Settings', 'pathdigital-discord-oauth' ); ?></a>
					<a id="tab-login-page" class="nav-tab <?php if( $current_tab == 'login_page' ) echo 'nav-tab-active'; ?>" href="admin.php?page=pd_discord_oauth_settings&tab=login_page"><?php esc_html_e( 'Login Page Design', 'pathdigital-discord-oauth' ); ?></a>
					<a id="tab-maintenance_page" class="nav-tab <?php if( $current_tab == 'maintenance_page' ) echo 'nav-tab-active'; ?>" href="admin.php?page=pd_discord_oauth_settings&tab=maintenance_page"><?php esc_html_e( 'Maintenance Page', 'pathdigital-discord-oauth' ); ?></a>
					<a id="tab-members" class="nav-tab <?php if( $current_tab == 'members' ) echo 'nav-tab-active'; ?>" href="admin.php?page=pd_discord_oauth_settings&tab=members"><?php esc_html_e( 'Members', 'pathdigital-discord-oauth' ); ?></a>
					<a id="tab-tools" class="nav-tab <?php if( $current_tab == 'tools' ) echo 'nav-tab-active'; ?>" href="admin.php?page=pd_discord_oauth_settings&tab=tools"><?php esc_html_e( 'Tools', 'pathdigital-discord-oauth' ); ?></a>
				</h2>
			</div>
			<?php
			if( $current_tab == '' ){
				?>
				
				<script>
					location.href = '<?php echo add_query_arg( array( 'tab' => 'oauth2' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>';
				</script>
				
				<?php
			}
			?>
			
			<div class="pd-settings-wrap">
				<div class="pd-has-cols">
					<div class="pd-content">
						<?php
						self::render_content( $current_tab );
						?>
					</div>
					<div class="pd-sidebar">
						<div class="pd-box">
							<h3>About path digital</h3>
							<img src="<?php echo plugin_dir_url( __FILE__ ); ?>/images/pathdigital_128x128.png" alt="pathdigital">
							<p>path digital is a digital agency that brings digital products on the path to success.</p>
							<p>With our experience and passion for technology and design, we strive for perfection and uniqueness in every project with the goal of adding value to your product.</p>
							<p>Let’s bring your product on the path to success.</p>
							<a href="https://discord.gg/wtR67uGRsK" class="button button-primary button-large" target="_blank">Join Discord</a>
						</div>
						<br>
						<iframe sandbox="allow-scripts" security="restricted" src="https://wordpress.org/plugins/sso-oauth-discord-by-path-digital/embed/" width="100%" title="SSO OAuth for Discord by path digital By path digital" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" class="wp-embedded-content" id="sizetracker"></iframe>
						<script type="text/javascript">!function(c,d){"use strict";var e=!1,n=!1;if(d.querySelector)if(c.addEventListener)e=!0;if(c.wp=c.wp||{},!c.wp.receiveEmbedMessage)if(c.wp.receiveEmbedMessage=function(e){var t=e.data;if(t)if(t.secret||t.message||t.value)if(!/[^a-zA-Z0-9]/.test(t.secret)){for(var r,a,i,s=d.querySelectorAll('iframe[data-secret="'+t.secret+'"]'),n=d.querySelectorAll('blockquote[data-secret="'+t.secret+'"]'),o=0;o<n.length;o++)n[o].style.display="none";for(o=0;o<s.length;o++)if(r=s[o],e.source===r.contentWindow){if(r.removeAttribute("style"),"height"===t.message){if(1e3<(i=parseInt(t.value,10)))i=1e3;else if(~~i<200)i=200;r.height=i}if("link"===t.message)if(a=d.createElement("a"),i=d.createElement("a"),a.href=r.getAttribute("src"),i.href=t.value,i.host===a.host)if(d.activeElement===r)c.top.location.href=t.value}}},e)c.addEventListener("message",c.wp.receiveEmbedMessage,!1),d.addEventListener("DOMContentLoaded",t,!1),c.addEventListener("load",t,!1);function t(){if(!n){n=!0;for(var e,t,r=-1!==navigator.appVersion.indexOf("MSIE 10"),a=!!navigator.userAgent.match(/Trident.*rv:11\./),i=d.querySelectorAll("iframe.wp-embedded-content"),s=0;s<i.length;s++){if(!(e=i[s]).getAttribute("data-secret"))t=Math.random().toString(36).substr(2,10),e.src+="#?secret="+t,e.setAttribute("data-secret",t);if(r||a)(t=e.cloneNode(!0)).removeAttribute("security"),e.parentNode.replaceChild(t,e)}}}}(window,document);</script>
					</div>
				</div>
			</div>
			
			<?php
		}

		/** Rendering the page content **/
		private static function render_content( $page ){
			
			if( $page == 'oauth2' ){
				
				$saved_oauth2_configuration = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
				$login_page = $saved_oauth2_configuration['login_page'];
			
				if( ! isset( $login_page ) ){
					
					$login_page  = ! empty( $page = get_page_by_path( 'oauth', 'OBJECT', 'page' ) ) ? $page->ID : false;
					
					if( ! $login_page )
						$login_page = wp_insert_post(
							array(
								'comment_status' => 'close',
								'ping_status' => 'close',
								'post_author' => 1,
								'post_title' => 'Login with Discord',
								'post_name' => 'oauth',
								'post_status' => 'publish',
								'post_content' => '',
								'post_type' => 'page',
								'post_parent' => 0
							)
						);
					
				}
				?>
				<div class="pd-box">
					<h3><?php esc_html_e( 'OAuth2 Settings', 'pathdigital-discord-oauth' ); ?></h3>
					<form name="update_conguration" method="POST" action="admin.php?page=pd_discord_oauth_settings&tab=oauth2&action=update_configuration">
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row">
									<label for="pd_login_page">Login Page <span class="pd-required">*</span></label>
								</th>
								<td>
									<select name="pd_login_page" id="pd_login_page" class="regular-text" required onchange="pd_update_redirect_url( this )">
										<option value="" disabled<?php echo ( ! isset( $login_page ) || get_post_status( intval( $login_page ) ) !== 'publish' ? ' selected' : '' ) ?>>Select...</option>
										<?php
										$pages = get_pages( array(
											'sort_order' => 'asc',
											'sort_column' => 'post_title',
											'post_type' => 'page',
											'post_status' => 'publish'
										) );
										foreach( $pages as $page ){
											if( intval( get_option( 'page_on_front' ) ) !== $page->ID )
												echo '<option value="' . $page->ID . '" data-url="' . get_permalink( $page->ID ) . '"' . ( intval( $login_page ) === $page->ID ? ' selected' : '' ) . '>' . $page->post_title . '</option>';
										}
										?>
									</select>
									<p class="description" style="font-size: 13px;">Page that shows the login button and is used as the redirect page</p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_redirect_url">Redirect URL <span class="pd-required">*</span></label>
								</th>
								<td>
									<input name="pd_redirect_url" type="text" id="pd_redirect_url" value="<?php echo esc_url( get_permalink( $login_page ) ) ; ?>" class="medium-text" required readonly>
									<button type="button" class="button gray pd-copy-to-clipboard" title="Copy to clipboard" onclick="pd_copy_to_clipboard( this )"></button>
								</td>
							<tr/>
							<tr>
								<th scope="row">
									<label for="pd_client_id">Client ID <span class="pd-required">*</span></label>
								</th>
								<td>
									<input name="pd_client_id" type="text" id="pd_client_id" value="<?php echo esc_attr( isset( $saved_oauth2_configuration['client_id'] ) ? $saved_oauth2_configuration['client_id'] : '' ); ?>" class="medium-text" required>
								</td>
							<tr/>
							<tr>
								<th scope="row">
									<label for="pd_client_secret">Client Secret <span class="pd-required">*</span></label>
								</th>
								<td>
									<input name="pd_client_secret" type="password" id="pd_client_secret" value="<?php echo esc_attr( isset( $saved_oauth2_configuration['client_secret'] ) ? $saved_oauth2_configuration['client_secret'] : '' ); ?>" class="medium-text" required>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_scope">Scope</label>
								</th>
								<td>
									<input name="pd_scope" type="text" id="pd_scope" value="<?php echo esc_attr( isset( $saved_oauth2_configuration['scope'] ) ? $saved_oauth2_configuration['scope'] : 'identify guilds' ); ?>" class="medium-text">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_authorization_url">Base Authorization URL <span class="pd-required">*</span></label>
								</th>
								<td>
									<input name="pd_authorization_url" type="url" id="pd_authorization_url" value="<?php echo esc_url( isset( $saved_oauth2_configuration['base_authorization_url'] ) ? $saved_oauth2_configuration['base_authorization_url'] : 'https://discordapp.com/api/oauth2/authorize' ); ?>" class="medium-text" required>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_token_url">Token URL <span class="pd-required">*</span></label>
								</th>
								<td>
									<input name="pd_token_url" type="url" id="pd_token_url" value="<?php echo esc_url( isset( $saved_oauth2_configuration['token_url'] ) ? $saved_oauth2_configuration['token_url'] : 'https://discordapp.com/api/oauth2/token' ); ?>" class="medium-text" required>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_user_info_endpoint">User Info Endpoint <span class="pd-required">*</span></label>
								</th>
								<td>
									<input name="pd_user_info_endpoint" type="url" id="pd_user_info_endpoint" value="<?php echo esc_url( isset( $saved_oauth2_configuration['user_info_endpoint'] ) ? $saved_oauth2_configuration['user_info_endpoint'] : 'https://discordapp.com/api/users/@me' ); ?>" class="medium-text" required>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_user_guilds_endpoint">User Guilds Endpoint <span class="pd-required">*</span></label>
								</th>
								<td>
									<input name="pd_user_guilds_endpoint" type="url" id="pd_user_guilds_endpoint" value="<?php echo esc_url( isset( $saved_oauth2_configuration['user_guilds_endpoint'] ) ? $saved_oauth2_configuration['user_guilds_endpoint'] : 'https://discordapp.com/api/users/@me/guilds' ); ?>" class="medium-text" required>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_server_validation_">Server Validation</label>
								</th>
								<td>
									<input type="checkbox" name="pd_server_validation" id="pd_server_validation" value="1"<?php echo ( isset( $saved_oauth2_configuration['server_validation'] ) && esc_attr( $saved_oauth2_configuration['server_validation'] ) == true ? ' checked' : '' ); ?>> <label for="pd_server_validation">Enable server validation</label>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_servers">Allowed Server List</label>
								</th>
								<td>
									<textarea name="pd_servers" id="pd_servers" class="medium-text" rows="4"><?php echo esc_textarea( isset( $saved_oauth2_configuration['server_ids'] ) ? implode( PHP_EOL, $saved_oauth2_configuration['server_ids'] ) : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_servers_blocked">Blocked Server List</label>
								</th>
								<td>
									<textarea name="pd_servers_blocked" id="pd_servers_blocked" class="medium-text" rows="4"><?php echo esc_textarea( isset( $saved_oauth2_configuration['server_ids_blocked'] ) ? implode( PHP_EOL, $saved_oauth2_configuration['server_ids_blocked'] ) : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_user_validation_">User Validation</label>
								</th>
								<td>
									<input type="checkbox" name="pd_user_validation" id="pd_user_validation" value="1"<?php echo ( isset( $saved_oauth2_configuration['user_validation'] ) && esc_attr( $saved_oauth2_configuration['user_validation'] ) == true ? ' checked' : '' ); ?>> <label for="pd_user_validation">Enable user validation</label>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_servers">Blocked User List</label>
								</th>
								<td>
									<textarea name="pd_users" id="pd_users" class="medium-text" rows="4"><?php echo esc_textarea( isset( $saved_oauth2_configuration['user_ids'] ) ? implode( PHP_EOL, $saved_oauth2_configuration['user_ids'] ) : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									&nbsp;
								</th>
								<td>
									<div class="notice notice-warning inline is-dismissible" style="margin: 0 0 1rem 0;">
										<p><strong>Note:</strong> If you are using any cache plugin like W3TC, remember to clear the cache after saving changes.</p>
									</div>
									<input type="submit" name="pd_submit_update_configuration" value="Save Changes" class="button button-primary button-large">
								</td>
							</tr>
						</table>
						<input type="hidden" name="method" value="pd_update_oauth2_configuration">
						<?php wp_nonce_field( 'pd_update_oauth2_configuration_form','pd_update_oauth2_configuration_form_field' ); ?>
					</form>
					<div class="pd-tip">
						<hr>
						Please refer <a href="https://discord.com/developers/docs/topics/oauth2" target="_blank">https://discord.com/developers/docs/topics/oauth2</a> for more information.
					</div>
				</div>
				<?php
			}
			
			if( $page == 'bot' ){
				
				$saved_bot_configuration = maybe_unserialize( get_option( '_pd_discord_bot_configuration_settings' ) );
				$saved_oauth2_configuration = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
				
				?>
				<div class="pd-box">
					<h3><?php esc_html_e( 'Bot Settings', 'pathdigital-discord-oauth' ); ?></h3>
					<form name="update_conguration" method="POST" action="admin.php?page=pd_discord_oauth_settings&tab=bot&action=update_configuration" id="pd_bot_configuration">
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row">
									<label for="pd_bot_tolken_">Token <span class="pd-required">*</span></label>
								</th>
								<td>
									<input name="pd_bot_tolken" type="password" id="pd_bot_tolken_" value="<?php echo esc_attr( isset( $saved_bot_configuration['bot_token'] ) ? $saved_bot_configuration['bot_token'] : '' ); ?>" class="medium-text" required>
									<?php if( ! isset( $saved_oauth2_configuration['client_id'] ) ) : ?>
										<div class="notice notice-warning inline" style="margin: 1em 0 0 0;">
											<p><strong>Warning:</strong> Please add the Client ID from <a href="admin.php?page=pd_discord_oauth_settings&tab=oauth2">OAuth2 Settings</a>.</p>
										</div>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_role_validation_">Role Validation</label>
								</th>
								<td>
									<input type="checkbox" name="pd_role_validation" id="pd_role_validation" value="1"<?php echo ( isset( $saved_bot_configuration['role_validation'] ) && esc_attr( $saved_bot_configuration['role_validation'] ) == true ? ' checked' : '' ); ?>> <label for="pd_role_validation">Enable role validation</label>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_validation_condition_">Validation Condition</label>
								</th>
								<td>
									<table style="margin-top: -0.7em;">
										<tr>
											<td style="vertical-align: top;"><strong>User role should be in</strong></td>
											<td>
												<div>
													<input type="radio" name="pd_validation_condition" id="pd_validation_condition_all" value="all"<?php echo ( isset( $saved_bot_configuration['validation_condition'] ) && esc_attr( $saved_bot_configuration['validation_condition'] ) == 'all' ? ' checked' : '' ); ?>> <label for="pd_validation_condition_all">All servers entered</label>
												</div>
												<div style="margin-top: 0.5rem;">
													<input type="radio" name="pd_validation_condition" id="pd_validation_condition_one" value="one"<?php echo ( isset( $saved_bot_configuration['validation_condition'] ) && esc_attr( $saved_bot_configuration['validation_condition'] ) == 'one' ? ' checked' : '' ); ?><?php echo ( ! isset( $saved_bot_configuration['validation_condition'] ) ? ' checked' : '' ); ?>> <label for="pd_validation_condition_one">At least one server</label>
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_bot_tolken">Server/Role Premission</label>
								</th>
								<td>
									<?php if( isset( $saved_bot_configuration['bot_token'] ) && ! empty ( $saved_bot_configuration['bot_token'] ) ) : ?>
										<?php
										$servers = $saved_oauth2_configuration['server_ids'];
										if( ! empty( $servers ) ) :
											$x = 1;
											$all_server_roles = array();
											foreach( $servers as $server ) :
												$body = $roles = array();
												$response = self::get_roles( $server );
												if( $response['response']['code'] === 200 ){
													
													$body = json_decode( $response['body'], true );
													$roles = $body['roles'];
													
													usort( $roles, function ( $item1, $item2 ){
														return $item1['position'] <=> $item2['position'];
													} );
													
												}
												$all_server_roles[] = array(
													'id' => $server,
													'name' => $body['name'],
													'roles' => $roles
												);
												?>
												<div class="pd-section pd-server">
													<table style="margin-top: -0.7em;">
														<tr>
															<td><strong>Server</strong></td>
															<td><?php echo $server . ( isset( $body['name'] ) && $body['name'] != '' ? ' <span style="color: #646970; font-style: italic;">(' . $body['name'] . ')</span>' : '' ); ?></td>
														<tr>
														<tr>
															<td><strong>Minimum Role</strong></td>
															<td>
																<?php
																if( ! empty( $roles ) ) : ?>
																	<select class="regular-text" name="pd_server[<?php echo $server; ?>]">
																		<?php
																		foreach( $roles as $role ) : ?>
																			<option value="<?php echo $role['id']; ?>"<?php echo ( $saved_bot_configuration['server_roles'][$server] == $role['id'] ? ' selected' : '' ); ?>><?php echo $role['name']; ?></option>
																		<?php endforeach; ?>
																	</select>
																<?php else: ?>
																	<div class="notice notice-error inline" style="margin: 0;">
																		<p><strong>Error:</strong> Bot does not have permission to access the server. <button type="button" onclick="popup('https://discord.com/oauth2/authorize?client_id=<?php echo $saved_oauth2_configuration['client_id'] ?>&scope=bot&permissions=0'); return false;" class="button button-secondary button-small">+ Add bot to the server</button></p>
																	</div>
																<?php endif; ?>
															</td>
														</tr>
														<tr>
															<td style="vertical-align: top;"><strong>Allowed Roles</strong></td>
															<td>
																<?php
																if( ! empty( $roles ) ) :
																	foreach( $roles as $role ) :
																		if( $server != $role['id'] ) : ?>
																		<div class="pd-server-role" style="display: inline-block; margin-right: 1em;">
																			<input type="checkbox" id="<?php echo $role['id']; ?>" name="pd_roles[<?php echo $server; ?>][]" value="<?php echo $role['id']; ?>"<?php echo ( isset( $saved_bot_configuration['allowed_roles'][$server] ) && in_array( $role['id'], $saved_bot_configuration['allowed_roles'][$server] ) ? ' checked' : '' ); ?>>
																			<label for="<?php echo $role['id']; ?>"><?php echo $role['name']; ?></label>
																		</div>
																	<?php endif;
																	endforeach; ?>
																<?php else: ?>
																	<div class="notice notice-error inline" style="margin: 0;">
																		<p><strong>Error:</strong> Bot does not have permission to access the server. <button type="button" onclick="popup('https://discord.com/oauth2/authorize?client_id=<?php echo $saved_oauth2_configuration['client_id'] ?>&scope=bot&permissions=0'); return false;" class="button button-secondary button-small">+ Add bot to the server</button></p>
																	</div>
																<?php endif; ?>
															</td>
														</tr>
													</table>
												</div>
												<?php if( $x < count( $servers ) ) : ?>
													<hr />
												<?php endif;
												$x++;
											endforeach;
										else : ?>
											<div class="notice notice-warning inline" style="margin: 0;">
												<p><strong>Warning:</strong> Please add servers to Server List from <a href="admin.php?page=pd_discord_oauth_settings&tab=oauth2">OAuth2 Settings</a>.</p>
											</div>
										<?php endif; ?>
									<?php else : ?>
										<div class="notice notice-error inline" style="margin: 0;">
											<p><strong>Error:</strong> Please enter a valid bot token and save changes before proceed.</p>
										</div>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_webhook_">Webhook URL</label>
								</th>
								<td>
									<input name="pd_webhook" type="url" id="pd_webhook_" value="<?php echo esc_url( isset( $saved_bot_configuration['webhook'] ) ? $saved_bot_configuration['webhook'] : '' ); ?>" class="medium-text">
									<p class="description" style="font-size: 13px;">Add your webhook URL here to log embeds or leave empty. <a href="https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks" target="_blank">Learn More about Discord webhooks</a></p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_bot_icon_">Bot Icon</label>
								</th>
								<td>
									<input name="pd_bot_icon" type="url" id="pd_bot_icon_" value="<?php echo esc_url( isset( $saved_bot_configuration['bot_icon'] ) ? $saved_bot_configuration['bot_icon'] : '' ); ?>" class="medium-text">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_embed_color_">Embed Color</label>
								</th>
								<td>
									<input name="pd_embed_color" type="text" id="pd_embed_color_" value="<?php echo esc_url( isset( $saved_bot_configuration['embed_color'] ) ? $saved_bot_configuration['embed_color'] : '' ); ?>" class="regular-text" maxlength="7" placeholder="#5865f2">
								</td>
							</tr>
							<tr>
								<th scope="row">
									&nbsp;
								</th>
								<td>
									<div class="notice notice-warning inline is-dismissible" style="margin: 0 0 1rem 0;">
										<p><strong>Note:</strong> If you are using any cache plugin like W3TC, remember to clear the cache after saving changes.</p>
									</div>
									<input type="submit" name="pd_submit_update_configuration" value="Save Changes" class="button button-primary button-large">
								</td>
							</tr>
						</table>
						<input type="hidden" name="method" value="pd_update_bot_configuration">
						<input type="hidden" name="pd_all_server_roles" value="<?php echo base64_encode(maybe_serialize($all_server_roles)) ?>">
						<?php wp_nonce_field( 'pd_update_bot_configuration_form','pd_update_bot_configuration_form_field' ); ?>
						<script>
						function popup( url ){
							var win = window.open( url, '', 'toolbar=no, location=no, width=700, height=700');
							var win_timer = setInterval( function(){   
								if( win.closed ){
									document.getElementById(  'pd_bot_configuration' ).submit();
									clearInterval(win_timer);
								} 
							}, 100); 
						}
						</script>

					</form>
					<div class="pd-tip">
						<hr>
						Please refer <a href="https://discord.com/developers/docs/topics/oauth2#bots" target="_blank">https://discord.com/developers/docs/topics/oauth2#bots</a> for more information.
					</div>
				</div>
				<?php
			}
			
			if( $page == 'general' ){
				
				$saved_general_configuration = maybe_unserialize( get_option( '_pd_discord_oauth_general_settings' ) );
				$excluded_pages = isset( $saved_general_configuration['excluded_pages'] ) ? $saved_general_configuration['excluded_pages'] : array();
				$excluded_posts = isset( $saved_general_configuration['excluded_posts'] ) ? $saved_general_configuration['excluded_posts'] : array();
				$hide_menus = isset( $saved_general_configuration['hide_menus'] ) ? esc_attr( $saved_general_configuration['hide_menus'] ) : true;
				$hide_widgets = isset( $saved_general_configuration['hide_widgets'] ) ? esc_attr( $saved_general_configuration['hide_widgets'] ) : true;
				$saved_oauth2_configuration = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
				$login_page = $saved_oauth2_configuration['login_page'];
				?>
				<div class="pd-box">
					<h3><?php esc_html_e( 'General Settings', 'pathdigital-discord-oauth' ); ?></h3>
					<form name="update_conguration" method="POST" action="admin.php?page=pd_discord_oauth_settings&tab=general&action=update_configuration" id="pd_general_configuration">
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row">
									<label for="pd_menus_">Whitelist</label>
								</th>
								<td>
									<?php
									$pages = get_pages( array( 'exclude' => array( $login_page ) ) );
									?>
									<select name="pd_excluded_pages[]" class="medium-text" multiple>
										<option value=""<?php echo ( empty( $excluded_pages ) ? ' selected' : '' ) ?>>None</option>
										<?php foreach( $pages as $page ) : ?>
											<option value="<?php echo $page->ID ?>"<?php echo ( in_array( $page->ID, $excluded_pages ) ? ' selected' : '' ) ?>><?php echo $page->post_title ?></option>
										<?php endforeach; ?>
									</select>
									<p class="description" style="font-size: 13px;">Accessible pages without login.</p>
									<br /><br />
									<?php
									$posts = get_posts( array( 'numberposts' => -1 ) );
									?>
									<select name="pd_excluded_posts[]" class="medium-text" multiple>
										<option value=""<?php echo ( empty( $excluded_posts ) ? ' selected' : '' ) ?>>None</option>
										<?php foreach( $posts as $post ) : ?>
											<option value="<?php echo $post->ID ?>"<?php echo ( in_array( $post->ID, $excluded_posts ) ? ' selected' : '' ) ?>><?php echo $post->post_title ?></option>
										<?php endforeach; ?>
									</select>
									<p class="description" style="font-size: 13px;">Accessible posts without login.</p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_menus_">Menus</label>
								</th>
								<td>
									<input type="checkbox" name="pd_menus" id="pd_menus_" value="1"<?php echo ( $hide_menus ? ' checked' : '' ); ?>> <label for="pd_menus_">Hide menus from unauthenticated users</label>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_widgets_">Widgets</label>
								</th>
								<td>
									<input type="checkbox" name="pd_widgets" id="pd_widgets_" value="1"<?php echo ( $hide_widgets ? ' checked' : '' ); ?>> <label for="pd_widgets_">Hide widgets from unauthenticated users</label>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_session_expiry">Session Duration</label>
								</th>
								<td>
									<input name="pd_session_expiry" type="number" id="pd_session_expiry" value="<?php echo esc_attr( isset( $saved_general_configuration['session_expiry'] ) ? $saved_general_configuration['session_expiry'] : 3 ); ?>" class="regular-text" min="0" max="7">
									<p class="description" style="font-size: 13px;">Session duration in days. Set 0 to expire on browser session end.</p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_success_redirect_page_">Redirect Page After Login</label>
								</th>
								<td>
									<?php
									
									?>
									<select name="pd_success_redirect_page" id="pd_success_redirect_page_" class="regular-text">
										<option value="0"<?php echo ( ! isset( $saved_general_configuration['success_redirect_page'] ) || get_post_status( intval( $saved_general_configuration['success_redirect_page'] ) ) !== 'publish' ? ' selected' : '' ) ?>>Select...</option>
										<?php
										$pages = get_pages( array(
											'sort_order' => 'asc',
											'sort_column' => 'post_title',
											'post_type' => 'page',
											'post_status' => 'publish',
											'exclude' => array( $login_page )
										) );
										foreach( $pages as $page ){
											echo '<option value="' . $page->ID . '"' . ( intval( $saved_general_configuration['success_redirect_page'] ) === $page->ID ? ' selected' : '' ) . '>' . $page->post_title . '</option>';
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_login_button_styles_">Login Button Styles</label>
								</th>
								<td>
									<textarea name="pd_login_button_styles" id="pd_login_button_styles_" class="medium-text" rows="10"><?php echo esc_textarea( isset( $saved_general_configuration['login_button_styles'] ) ? implode( PHP_EOL, array_map( function( $button_style ){ return( $button_style . ';' ); }, $saved_general_configuration['login_button_styles'] ) ) : '' ); ?></textarea>
									<p class="description" style="font-size: 13px;">One declaration per line. Leave this empty to use default theme styles.</p>
									<p class="description" style="font-size: 13px;">
										<code style="padding-left: 0; padding-right: 0;">background: #5865f2;<br>
										border-color: #5865f2;<br>
										color: #fff;<br>
										border-radius: 5px;<br>
										text-decoration: none;<br>
										font-weight: 500</code>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									&nbsp;
								</th>
								<td>
									<div class="notice notice-warning inline is-dismissible" style="margin: 0 0 1rem 0;">
										<p><strong>Note:</strong> If you are using any cache plugin like W3TC, remember to clear the cache after saving changes.</p>
									</div>
									<input type="submit" name="pd_submit_update_configuration" value="Save Changes" class="button button-primary button-large">
								</td>
							</tr>
						</table>
						<input type="hidden" name="method" value="pd_update_general_configuration">
						<?php wp_nonce_field( 'pd_update_general_configuration_form','pd_update_general_configuration_form_field' ); ?>
					</form>
				</div>
				<?php
			}
			
			if( $page == 'login_page' ){
				
				$saved_login_page_configuration = maybe_unserialize( get_option( '_pd_discord_oauth_login_page_settings' ) );
				$login_page = isset( $saved_login_page_configuration['login_page'] ) ? esc_attr( $saved_login_page_configuration['login_page'] ) : true;
				
				?>
				<div class="pd-box">
					<h3><?php esc_html_e( 'Login Page Design', 'pathdigital-discord-oauth' ); ?></h3>
					<form name="update_conguration" method="POST" action="admin.php?page=pd_discord_oauth_settings&tab=login_page&action=update_configuration" id="pd_login_page_configuration">
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row">
									<label for="pd_login_page_">Login Page</label>
								</th>
								<td>
									<input type="checkbox" name="pd_login_page" id="pd_login_page_" value="1"<?php echo ( $login_page ? ' checked' : '' ); ?>> <label for="pd_login_page_">Enable custom login page</label>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_login_page_logo_">Logo URL</label>
								</th>
								<td>
									<textarea name="pd_login_page_logo" id="pd_login_page_logo_" class="medium-text" rows="2"><?php echo esc_url( isset( $saved_login_page_configuration['logo'] ) ? $saved_login_page_configuration['logo'] : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_login_page_heading_">Heading</label>
								</th>
								<td>
									<textarea name="pd_login_page_heading" id="pd_login_page_heading_" class="medium-text" rows="2"><?php echo esc_textarea( isset( $saved_login_page_configuration['heading'] ) ? $saved_login_page_configuration['heading'] : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_login_page_subtext_">Subtext</label>
								</th>
								<td>
									<textarea name="pd_login_page_subtext" id="pd_login_page_subtext_" class="medium-text" rows="4"><?php echo esc_textarea( isset( $saved_login_page_configuration['subtext'] ) ? $saved_login_page_configuration['subtext'] : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_login_page_background_color_">Background Color</label>
								</th>
								<td>
									<input type="text" name="pd_login_page_background_color" id="pd_login_page_background_color_" class="regular-text" maxlength="7" value="<?php echo esc_html( isset( $saved_login_page_configuration['background_color'] ) ? $saved_login_page_configuration['background_color'] : '#808080' ); ?>" placeholder="#808080">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_login_page_background_image_">Background Image URL</label>
								</th>
								<td>
									<textarea name="pd_login_page_background_image" id="pd_login_page_background_image_" class="medium-text" rows="3"><?php echo esc_url( isset( $saved_login_page_configuration['background_image'] ) ? $saved_login_page_configuration['background_image'] : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_login_page_banned_error_">Banned User Error</label>
								</th>
								<td>
									<input type="text" name="pd_login_page_banned_error" id="pd_login_page_banned_error_" class="medium-text" value="<?php echo esc_html( isset( $saved_login_page_configuration['banned_error'] ) ? wp_unslash( $saved_login_page_configuration['banned_error'] ) : '' ); ?>">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_login_page_no_role_error_">Role Validation Error</label>
								</th>
								<td>
									<input type="text" name="pd_login_page_no_role_error" id="pd_login_page_no_role_error_" class="medium-text" value="<?php echo esc_html( isset( $saved_login_page_configuration['no_role_error'] ) ? wp_unslash( $saved_login_page_configuration['no_role_error'] ) : '' ); ?>">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_login_page_banned_error_">Server Validation Error</label>
								</th>
								<td>
									<input type="text" name="pd_login_page_not_in_server_error" id="pd_login_page_not_in_server_error_" class="medium-text" value="<?php echo esc_html( isset( $saved_login_page_configuration['not_in_server_error'] ) ? wp_unslash( $saved_login_page_configuration['not_in_server_error'] ) : '' ); ?>">
								</td>
							</tr>
							<tr>
								<th scope="row">
									&nbsp;
								</th>
								<td>
									<div class="notice notice-warning inline is-dismissible" style="margin: 0 0 1rem 0;">
										<p><strong>Note:</strong> If you are using any cache plugin like W3TC, remember to clear the cache after saving changes.</p>
									</div>
									<input type="submit" name="pd_submit_update_configuration" value="Save Changes" class="button button-primary button-large">
								</td>
							</tr>
						</table>
						<input type="hidden" name="method" value="pd_update_login_page_configuration">
						<?php wp_nonce_field( 'pd_update_login_page_configuration_form','pd_update_login_page_configuration_form_field' ); ?>
					</form>
				</div>
				<?php
			}
			
			if( $page == 'maintenance_page' ){
				
				$saved_maintainance_page_configuration = maybe_unserialize( get_option( '_pd_discord_oauth_maintainance_page_settings' ) );
				$maintainance_mode = isset( $saved_maintainance_page_configuration['maintainance_mode'] ) ? esc_attr( $saved_maintainance_page_configuration['maintainance_mode'] ) : false;
				
				?>
				<div class="pd-box">
					<h3><?php esc_html_e( 'Maintenance Page', 'pathdigital-discord-oauth' ); ?></h3>
					<form name="update_conguration" method="POST" action="admin.php?page=pd_discord_oauth_settings&tab=maintenance_page&action=update_configuration" id="pd_maintenance_page_configuration">
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row">
									<label for="pd_login_page_">Maintenance Mode</label>
								</th>
								<td>
									<input type="checkbox" name="pd_maintainance_mode" id="pd_maintainance_mode_" value="1"<?php echo ( $maintainance_mode ? ' checked' : '' ); ?>> <label for="pd_maintainance_mode_">Enable maintenance mode</label>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_maintainance_page_logo_">Logo URL</label>
								</th>
								<td>
									<textarea name="pd_maintainance_page_logo" id="pd_maintainance_page_logo_" class="medium-text" rows="2"><?php echo esc_url( isset( $saved_maintainance_page_configuration['logo'] ) ? $saved_maintainance_page_configuration['logo'] : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_maintainance_page_heading_">Heading</label>
								</th>
								<td>
									<textarea name="pd_maintainance_page_heading" id="pd_maintainance_page_heading_" class="medium-text" rows="2"><?php echo esc_textarea( isset( $saved_maintainance_page_configuration['heading'] ) ? $saved_maintainance_page_configuration['heading'] : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_maintainance_page_subtext_">Subtext</label>
								</th>
								<td>
									<textarea name="pd_maintainance_page_subtext" id="pd_maintainance_page_subtext_" class="medium-text" rows="4"><?php echo esc_textarea( isset( $saved_maintainance_page_configuration['subtext'] ) ? $saved_maintainance_page_configuration['subtext'] : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_maintainance_page_background_color_">Background Color</label>
								</th>
								<td>
									<input type="text" name="pd_maintainance_page_background_color" id="pd_maintainance_page_background_color_" class="regular-text" maxlength="7" value="<?php echo esc_html( isset( $saved_maintainance_page_configuration['background_color'] ) ? $saved_maintainance_page_configuration['background_color'] : '#808080' ); ?>" placeholder="#808080">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="pd_maintainance_page_background_image_">Background Image URL</label>
								</th>
								<td>
									<textarea name="pd_maintainance_page_background_image" id="pd_maintainance_page_background_image_" class="medium-text" rows="3"><?php echo esc_url( isset( $saved_maintainance_page_configuration['background_image'] ) ? $saved_maintainance_page_configuration['background_image'] : '' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									&nbsp;
								</th>
								<td>
									<div class="notice notice-warning inline is-dismissible" style="margin: 0 0 1rem 0;">
										<p><strong>Note:</strong> If you are using any cache plugin like W3TC, remember to clear the cache after saving changes.</p>
									</div>
									<input type="submit" name="pd_submit_update_configuration" value="Save Changes" class="button button-primary button-large">
								</td>
							</tr>
						</table>
						<input type="hidden" name="method" value="pd_update_maintainance_page_configuration">
						<?php wp_nonce_field( 'pd_update_maintainance_page_configuration_form','pd_update_maintainance_page_configuration_form_field' ); ?>
					</form>
				</div>
				<?php
			}
			
			if( $page == 'members' ){
				?>
				<div class="pd-box">
					<h3><?php esc_html_e( 'Members', 'pathdigital-discord-oauth' ); ?></h3>
					<?php
					$user_sessions = $temp_users = array();
					$login_sessions = maybe_unserialize( get_option( '_pd_discord_oauth_user_sessions' ) );
					$oauth_configuration_settings = maybe_unserialize( get_option( '_pd_discord_oauth_configuration_settings' ) );
					$blocked_users = $oauth_configuration_settings['user_ids'];
					
					foreach( $login_sessions as $key => $value ){
						
						$user_sessions[] = array(
							'session' => $key,
							'user_id' => $value['user']['id'],
							'username' => $value['user']['username'],
							'avatar' =>  $value['user']['avatar'],
							'discriminator' => $value['user']['discriminator'],
							'user_roles' => $value['user_roles'],
							'created' => $value['created'],
							'servers' => $value['servers']
						);
						
					}
					
					usort( $user_sessions, function( $item1, $item2 ){
						return $item2['created'] <=> $item1['created'];
					} );
					
					?>
					
					<table class="wp-list-table widefat fixed striped table-view-list users">
						<thead>
							<tr>
								<td class="check-column"></td>
								<th class="manage-column" scope="col">Username</th>
								<th class="manage-column" scope="col">User ID</th>
								<th class="manage-column" scope="col">Role(s)</th>
								<th class="manage-column" scope="col">Other Servers</th>
								<th class="manage-column" scope="col">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php
							
							foreach( $user_sessions as $key => $value ){
								if( ! in_array( $value['user_id'], $temp_users ) ){
									
									$temp_users[] = $value['user_id']; ?>
									<tr>
										<th class="check-column"><?php echo $key + 1 ?></th>
										<td class="username column-username has-row-actions column-primary">
											<img class="avatar avatar-32 photo" src="https://cdn.discordapp.com/avatars/<?php echo $value['user_id'] ?>/<?php echo $value['avatar'] ?>.png" width="32" height="32">
											<strong><?php echo $value['username'] ?>#<?php echo $value['discriminator'] ?></strong>
										</td>
										<td class="column-primary"><?php echo $value['user_id'] ?></td>
										<td class="column-primary">
											<ol style="margin-top: 0; margin-left: 0.8rem;">
												<?php
												$i = 1;
												foreach( $value['user_roles'] as $key2 => $value2 ){
													
													$server_info = self::get_roles($key2);
													
													if( $server_info['response']['code'] === 200 ){
														
														$server_info_array = json_decode( $server_info['body'], true ); ?>
														<li>
															<strong><?php echo $server_info_array['name'] ?></strong>
															<ul style="list-style: circle;">
																<?php
																foreach( $server_info_array['roles'] as $role ){
																	if( in_array( $role['id'], $value2 ) && $role['id'] != $key2 ){ ?>
																		<li style="margin: 0;"><span><?php echo $role['name'] ?></span></li>
																	<?php
																	}
																} ?>
															</ul>
														</li>
													<?php }
												} ?>
											</ol>
										</td>
										<td>
										<?php
										$knowns = $others = array();
										foreach( $value['user_roles'] as $key => $known )
											$knowns[] = $key;
											
										$all_servers = isset( $value['servers'] ) ? $value['servers'] : array();
										foreach( $all_servers as $server )
											if( ! in_array( $server['id'], $knowns ) && isset( $server['name'] ) )
												$others[] = $server['name'];
											
										echo '<p>' . implode( ', ', $others ) . '</p>';
										?>
										</td>
										<td class="column-primary">
											<div style="white-space: nowrap;">
												<button class="button-secondary" onclick="blockUser( '<?php echo $value['user_id'] ?>', this );"><?php echo ( in_array( $value['user_id'], $blocked_users ) ? 'Unblock' : 'Block' ) ?></button>
												<button class="button-secondary" onclick="forceLogout( '<?php echo $value['user_id'] ?>', this );">Force Logout</button>
											</div>
										</td>
									</tr>
									
								<?php }	
							} ?>
						</tbody>
					</table>
					<div class="notice notice-warning inline is-dismissible" style="margin: 1rem 0 0 0;">
						<p><strong>Note:</strong> The members you see here are taken from logging sessions. This list may change automatically, as inactive login sessions are automatically removed. However, blocked members will always be blocked until you unblock them.</p>
						<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
					</div>
				</div>
				<?php
			}
			
			if( $page == 'tools' ){
				?>
				<div class="pd-box">
					<h3><?php esc_html_e( 'Tools', 'pathdigital-discord-oauth' ); ?></h3>
					<form name="clearLogin_sessions" method="POST" action="admin.php?page=pd_discord_oauth_settings&tab=tools&action=clear_login_sessions" id="pd_clear_login_sessions"  onsubmit="return confirm('Do you really want to clear all the active login sessions?');">
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row">
									<?php esc_html_e( 'Login Sessions', 'pathdigital-discord-oauth' ); ?>
								</th>
								<td>
									<input type="submit" name="pd_submit_clear_login_sessions" value="Clear All" class="button button-primary button-large">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e( 'Logout shortcodes', 'pathdigital-discord-oauth' ); ?>
								</th>
								<td>
									<textarea class="medium-text" readonly rows="3">[pd_logout]&#10;[pd_logout label="Discord Logout"]&#10;[pd_logout label="Discord Logout" button="1"]</textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e( 'Logout URL', 'pathdigital-discord-oauth' ); ?>
								</th>
								<td>
									<textarea class="medium-text" readonly rows="1"><?php echo get_site_url() . ( parse_url( get_site_url(), PHP_URL_QUERY ) ? '&' : '?' ) ?>pd_discord_oauth_logout=1</textarea>
								</td>
							</tr>
						</table>
						<input type="hidden" name="method" value="pd_clear_login_sessions">
						<?php wp_nonce_field( 'pd_clear_login_sessions_form','pd_clear_login_sessions_form_field' ); ?>
					</form>
				</div>
				<?php
			}
			
		}
		
		/** Get roles **/
		public static function get_roles( $server ){
			
			$endpoint = 'https://discordapp.com/api/guilds/' . $server;
			$saved_bot_configuration = maybe_unserialize( get_option( '_pd_discord_bot_configuration_settings' ) );
			$headers = array(
				'Accept'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Bot ' . $saved_bot_configuration['bot_token'],
				'Content-Type' => 'application/x-www-form-urlencoded',
            );
				
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
			
			return array (
				'response' => $response['response'],
				'body' => $response['body']
			);
			
		}
		
		/** Get role **/
		public static function get_role( $server, $role ){
			
			$endpoint = 'https://discordapp.com/api/guilds/' . $server . '/roles/' . $role;
			$saved_bot_configuration = maybe_unserialize( get_option( '_pd_discord_bot_configuration_settings' ) );
			$headers = array(
				'Accept'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Bot ' . $saved_bot_configuration['bot_token'],
				'Content-Type' => 'application/x-www-form-urlencoded',
            );
				
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
			
			return array (
				'response' => $response['response'],
				'body' => $response['body']
			);
			
		}

	}
	
}
?>