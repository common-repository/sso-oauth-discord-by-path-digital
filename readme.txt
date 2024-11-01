=== SSO OAuth for Discord by path digital ===
Contributors: path digital
Tags: Discord, SSO, Login, OAuth, Discord Login, Social Login
Requires at least: 4.0
Tested up to: 6.3.1
Requires PHP: 5.4
Stable tag: 3.1.3
License: GPLv2 or later

Discord OAuth for your Website. Hide your website content with Discord SSO and make it only available for your server members.

== Description ==
SSO OAuth for Discord by path digital allows you to hide your website content with Discord SSO and make it only available for your server members.

== Installation ==
= From your WordPress dashboard =
1. Visit `Plugins -> Add New`
2. Search for `SSO OAuth for Discord` and Install the plugin by path digital
3. Activate the plugin from your Plugins page

= From WordPress.org =
1. Download SSO OAuth for Discord by path digital
2. Unzip and upload the folder directory to your `/wp-content/plugins/` directory
3. Activate the plugin from your Plugins page

= Once Activated =
1. Go to `Discord OAuth -> OAuth2 Settings`, and select the login page you want to show the login button
2. Create your Discord Application from <a href="https://discord.com/developers/applications" target="_blank">Applications</a> with the `Redirect URL` you copied from `Configure` page
3. Enter Discord Application data in to the `Configuration` page and save settings
4. Visit your website now, if you are not logged in, the website will ask you to login

== Frequently Asked Questions ==
= How can I setup a Discord application? =
Visit <a href="https://discord.com/developers/applications/" target="_blank">https://discord.com/developers/applications/</a> and add a New Application.

= From where I can get the Client ID and Client Secret? =
Go to your <a href="https://discord.com/developers/applications/" target="_blank">applications</a> and click on the newly created application. Click the OAuth2 tab in the navigation panel, where you can copy both the Client ID and Client Secret.

= From where I can get the Token for the Bot? =
Click on the newly created application for OAuth2 from <a href="https://discord.com/developers/applications/" target="_blank">applications</a>. Click the Bot tab in the navigation panel and Add Bot to get the Token.

= How to get the server ID from Discord =
Login to your Discord account, Go to User Settings by clicking on the gear icon next to your username. Go to Advanced Settings in App Settings section and turn ON the Developer Mode. Close the settings page and go to your Discord Dashboard, right click on any server name and Copy ID. 

== Screenshots ==
1. OAuth2 settings
2. Bot settings
3. General settings
4. Custom login page settings
5. Embed on any page
6. Custom login page
7. Login button on a page
8. Channel embeds via webhook
9. Maintenance mode settings
10. Maintenance page
11. Tools
12. Manage members

== Changelog ==
= 3.1.3 =
* Bug fixes

= 3.1.2 =
* Bug fixes

= 3.1.1 =
* Fixed typo, "maintenance" to "maintainance" in UI.

= 3.1.0 =
* Added admin option to view all servers the user is in

* Bug fixes
= 3.0.5 =
* Bug fixes

= 3.0.4 =
* Bug fixes

= 3.0.3 =
* Bug fixes

= 3.0.2 =
* Bug fixes

= 3.0.1 =
* Bug fixes

= 3.0.0 =
* Added maintenance mode page
* Added option to view active user sessions and manage users
* Removed menu items of pages with minimum user role for people that don't have the minimum role
* Added shortcode to display a logout button

= 2.5.4 =
* Bug fixes

= 2.5.3 =
* Bug fixes

= 2.5.2 =
* Minor code enhancements

= 2.5.1 =
* Bug fixes

= 2.5.0 =
* Added option to whitelist pages for specific user role(s)

= 2.4.0 =
* Added option to whitelist user roles independant from minimum role
* Minor code enhancements

= 2.3.1 =
* Bug fixes

= 2.3.0 =
* Added option to clear all the active login sessions
* Updated Discord embeds to post whitelisted logins

= 2.2.0 =
* Added option to set login errors
* Added option to post failed login attempts embeds on a Discord channel via a Webhook
* Added option to set the redirect page after a successful login

= 2.1.1 =
* Bug fixes

= 2.1.0 =
* Updated option to hide menus from unauthenticated users
* Updated option to hide widgets from unauthenticated users

= 2.0.0 =
* Added option to block users by Discord ID
* Added option to allow user to view specific pages and posts
* Added option to add a custom login page
* Added option to post successful login embeds on a Discord channel via a Webhook
* Disabled admin ajax for non-logged users
* Minor code enhancements

= 1.5.1 =
* Bug fixes

= 1.5.0 =
* Added option to blacklist servers

= 1.4.2 =
* Bug fixes

= 1.4.1 =
* Bug fixes

= 1.4.0 =
* Added option to set the session duration
* Moved the login error message to the login page
* Security enhancements

= 1.3.1 =
* Bug fixes

= 1.3.0 =
* Added custom login button styles support
* Added option to show/hide menus in login page
* Added option to show/hide widgets in login page
* Added functionality to auto create and assign the login page

= 1.2.1 =
* Added server name next to the Server ID in Bot Settings page
* Added settings link to plugin action links
* Minor visual improvements to admin pages

= 1.2.0 =
* Added user role validation support
* Added the ability to enable / disable server validation even if the server list is not empty

= 1.1.0 =
* Bug fixes
* Skiped server validation for 5 minutes to limit Discord API calls

= 1.0.2 =
* Bug fixes and patches

= 1.0.1 =
* Initial Release