			<!-- footer -->
			<footer>
				<div class="box">
					<div class="infoFooter blockPhone">
						<a href="#" class="logoFooter hidden-phone">
							<img src="<?php echo get_template_directory_uri(); ?>/img/aba_footer.png" alt="ABA rent a car">
						</a>
						<div class="infoBox blockPhone">

							<?php if (qtranxf_getLanguage() == 'es') { ?>
								<?php echo of_get_option('footer_textarea_es'); ?>
							<?php } else if (qtranxf_getLanguage() == 'en') { ?>
								<?php echo of_get_option('footer_textarea_en'); ?>
							<?php } else { ?>
								<?php echo of_get_option('footer_textarea_br'); ?>
							<?php } ?>

						</div>
					</div>
					<div class="menuFooter visible-desktop">
						<?php if (qtranxf_getLanguage() == 'es') { ?>
							<h4>Menú</h4>
						<?php } else if (qtranxf_getLanguage() == 'en') { ?>
							<h4>Menu</h4>
						<?php } else { ?>
							<h4>Menu</h4>
						<?php } ?>
						<?php footer_nav(); ?>
					</div>
					<div class="redesFooter blockPhone">
						<?php if (qtranxf_getLanguage() == 'es') { ?>
							<h4>Redes Sociales</h4>
						<?php } else if (qtranxf_getLanguage() == 'en') { ?>
							<h4>Social networks</h4>
						<?php } else { ?>
							<h4>Redes sociais</h4>
						<?php } ?>


						<a href="<?php echo of_get_option('tel'); ?>" rel="external" class="visible-phone icon phone" target="_blank">
							<i class="fa fa-phone"></i>
						</a>

						<a href="https://api.whatsapp.com/send?phone=5492944604766" onclick="return gtag_report_conversion('https://wa.me/5492944604766')" rel="external" class="visible-phone icon whatsapp" target="_blank">
							<i class="fa fa-whatsapp"></i>
						</a>

						<?php if (of_get_option('facebook')) { ?>
							<a href="<?php echo of_get_option('facebook'); ?>" rel="external" class="icon facebook" target="_blank">
								<i class="fa fa-facebook"></i>
							</a>
						<?php } ?>
						<?php if (of_get_option('twitter')) { ?>
							<a href="<?php echo of_get_option('twitter'); ?>" rel="external" class="icon twitter" target="_blank">
								<i class="fa fa-twitter"></i>
							</a>
						<?php } ?>
						<?php if (of_get_option('googleplus')) { ?>
							<a href="<?php echo of_get_option('googleplus'); ?>" rel="external" class="icon googleplus" target="_blank">
								<i class="fa fa-google-plus"></i>
							</a>
						<?php } ?>
						<?php if (of_get_option('linkedin')) { ?>
							<a href="<?php echo of_get_option('linkedin'); ?>" rel="external" class="icon linkedin" target="_blank">
								<i class="fa fa-linkedin"></i>
							</a>
						<?php } ?>
						<?php if (of_get_option('pinterest')) { ?>
							<a href="<?php echo of_get_option('pinterest'); ?>" rel="external" class="icon pinterest" target="_blank">
								<i class="fa fa-pinterest"></i>
							</a>
						<?php } ?>
						<?php if (of_get_option('instagram')) { ?>
							<a href="<?php echo of_get_option('instagram'); ?>" rel="external" class="icon instagram" target="_blank">
								<i class="fa fa-instagram"></i>
							</a>
						<?php } ?>
					</div>

					<div class="clearfix"></div>
					<div class="design">
						<?php if (qtranxf_getLanguage() == 'es') { ?>
							Diseño y desarrollo
						<?php } else if (qtranxf_getLanguage() == 'en') { ?>
							Design and development
						<?php } else { ?>
							Diseño y desarrollo
						<?php } ?>
						<a href="http://klimavisual.com" target="_blank" class="klima">KLIMA</a>
					</div>
				</div>
			</footer>
			<!-- /footer -->

			<?php wp_footer(); ?>



			</body>

			</html>