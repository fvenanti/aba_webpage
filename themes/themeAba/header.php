<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>

		<link rel="dns-prefetch" href="https://www.google-analytics.com">
		<link rel="dns-prefetch" href="https://www.googletagmanager.com">
		<link href="https://www.google-analytics.com" rel="preconnect" crossorigin>
		<link href="https://www.googletagmanager.com" rel="preconnect" crossorigin>

		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-WWE1SRJF5Z"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', 'G-WWE1SRJF5Z');
		</script>


		<!-- Event snippet for Whatsapp + Formulario conversion page
		In your html page, add the snippet and call gtag_report_conversion when someone clicks on the chosen link or button. -->
		<script>
			function gtag_report_conversion(url) {
			var callback = function () {
				if (typeof(url) != 'undefined') {
				window.location = url;
				}
			};
			gtag('event', 'conversion', {
				'send_to': 'AW-808820267/Y87oCIiUheUBEKu81oED',
				'event_callback': callback
			});
			return false;
			}
		
		</script>
			
        <link href="<?php echo get_template_directory_uri(); ?>/img/icons/favicon.ico" rel="shortcut icon">
        <link href="<?php echo get_template_directory_uri(); ?>/img/icons/touch.png" rel="apple-touch-icon-precomposed">

		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<?php wp_head(); ?>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,400italic,700' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css2?family=Raleway:wght@300;600&display=swap' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

		<!-- Facebook Pixel Code -->
		<script>
			!function(f,b,e,v,n,t,s)
			{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};
			if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
			n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];
			s.parentNode.insertBefore(t,s)}(window, document,'script',
			'https://connect.facebook.net/en_US/fbevents.js');
			fbq('init', '352002245889127');
			fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=352002245889127&ev=PageView&noscript=1"
		/></noscript>
		<!-- End Facebook Pixel Code -->


		<script>
        // conditionizr.com
        // configure environment tests
        conditionizr.config({
            assets: '<?php echo get_template_directory_uri(); ?>',
            tests: {}
        });
        </script>

	</head>
	<body <?php body_class(); ?>>

		<header>
			<div class="box">
				<a href="<?php echo home_url(); ?>" class="logo"></a>
				<div id="menu">
					<!-- nav -->
					<nav class="nav" role="navigation" id="menuResp">
						<?php html5blank_nav(); ?>
					</nav>
					<!-- /nav -->
					<div class="toolsBox">
						<div class="redes">

							<a href="<?php echo of_get_option('tel'); ?>" rel="external" class="visible-phone icon phone" target="_blank">
								<i class="fa fa-phone"></i>
							</a>

							<a href="https://api.whatsapp.com/send?phone=5492944604766" onclick="return gtag_report_conversion('https://wa.me/5492944604766')" rel="external" class="visible-phone icon whatsapp" target="_blank">
								<i class="fa fa-whatsapp"></i>
							</a>

							<?php if ( of_get_option('facebook') ) { ?>
								<a href="<?php echo of_get_option('facebook'); ?>" rel="external" class="icon facebook" target="_blank">
									<i class="fa fa-facebook"></i>
								</a>
							<?php } ?>
							<?php if ( of_get_option('twitter') ) { ?>
								<a href="<?php echo of_get_option('twitter'); ?>" rel="external" class="icon twitter" target="_blank">
									<i class="fa fa-twitter"></i>
								</a>
							<?php } ?>
							<?php if ( of_get_option('googleplus') ) { ?>
								<a href="<?php echo of_get_option('googleplus'); ?>" rel="external" class="icon googleplus" target="_blank">
									<i class="fa fa-google-plus"></i>
								</a>
							<?php } ?>
							<?php if ( of_get_option('linkedin') ) { ?>
								<a href="<?php echo of_get_option('linkedin'); ?>" rel="external" class="icon linkedin" target="_blank">
									<i class="fa fa-linkedin"></i>
								</a>
							<?php } ?>
							<?php if ( of_get_option('pinterest') ) { ?>
								<a href="<?php echo of_get_option('pinterest'); ?>" rel="external" class="icon pinterest" target="_blank">
									<i class="fa fa-pinterest"></i>
								</a>
							<?php } ?>
							<?php if ( of_get_option('instagram') ) { ?>
								<a href="<?php echo of_get_option('instagram'); ?>" rel="external" class="icon instagram" target="_blank">
									<i class="fa fa-instagram"></i>
								</a>
							<?php } ?>
                            <a href="https://wa.me/5492944604766" rel="external" class="icon instagram" target="_blank">
									<i class="fa fa-whatsapp"></i>
								</a>
						</div>
						<div class="lang">
							<i class="fa fa-flag"></i>
							<span class="links">
								<?php get_template_part('top-widget'); ?>
							</span>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</header>