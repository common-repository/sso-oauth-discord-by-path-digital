<?php
/**
 * Template Name: SSO OAuth for Discord Maintainance Page
 * 
 */
?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >

		<link rel="profile" href="https://gmpg.org/xfn/11">

		<?php wp_head(); ?>
		<style>
		.pd-maintainance-box-wrap {
			position: fixed;
			top: 50%;
			left: 50%;
			-webkit-transform: translateY(-50%) translateX(-50%);
			    -ms-transform: translateY(-50%) translateX(-50%);
			        transform: translateY(-50%) translateX(-50%);
			max-width: 400px;
			min-width: 300px;
			padding: 30px;
			z-index: 100;
			background: #fff;
			border-radius: 20px;
			-webkit-box-shadow: 0px 0px 40px 10px rgba(0, 0, 0, 0.05);
			        box-shadow: 0px 0px 40px 10px rgba(0, 0, 0, 0.05);
		}
		.pd-maintainance-logo-wrap {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-align: center;
			    -ms-flex-align: center;
			        align-items: center;
			-webkit-box-pack: center;
			    -ms-flex-pack: center;
			        justify-content: center;
			width: 128px;
			height: 128px;
			border-radius: 50%;
			overflow: hidden;
			margin-left: auto;
			margin-right: auto;
			margin-bottom: 20px;
		}
		.pd-maintainance-logo-wrap img {
			width: 100%;
			height: 100%;
			-o-object-fit: cover;
			   object-fit: cover;
		}
		.pd-maintainance-heading {
			text-align: center;
			font-size: 24px;
			font-weight: 700;
			padding-bottom: 20px;
			border-bottom: 1px solid #eaeaea;
			margin-bottom: 20px;
			color: #303030;
		}
		.pd-maintainance-subtext {
			font-size: 18px;
			color: #808080;
			line-height: 1.4;
			text-align: center;
			margin-bottom: 30px;
		}
		.pd_sso_maintainance_page .pd-maintainance-button-wrap {
			padding: 0;
		}
		.pd_sso_maintainance_page .wp-block-button {
			width: 100%;
		}
		.pd_sso_maintainance_page .button {
			width: 100%;
			padding: 10px;
			font-weight: 400;
			background: #5865f2;
			color: #fff;
			border-radius: 5px;
			display: block;
			text-align: center;
			-webkit-box-sizing: border-box;
			        box-sizing: border-box;
			text-decoration: none;
		}
		.pd-maintainance-background {
			position: fixed;
			z-index: 1;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			background-position: 50% 50%;
			background-repeat: no-repeat;
			background-size: cover;
		}
		</style>
	</head>

	<body class="pd_sso_maintainance_page">
	
		<?php the_content(); ?>
				
		<?php wp_footer(); ?>
		
	</body>
</html>