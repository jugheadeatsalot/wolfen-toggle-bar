<?php
/*
Plugin Name: Wolfen Toggle Bar
Description: Makes the default WordPress adminbar toggleable.
Author: John Romeral
Author URI: http://johnromeral.com
Plugin URI: https://github.com/jugheadeatsalot/wolfen-toggle-bar
Version: 0.0.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
if(!class_exists('Wolfen_Toggle_Bar')) {
	class Wolfen_Toggle_Bar {
		protected $_buttonClass = 'wtb-button';
		protected $_hiddenClass = 'wtb-hidden';

		public function __construct() {
			add_action('wp_enqueue_scripts', array($this, 'scripts'));
			add_action('wp_head', array($this, 'style'), 999);
			add_action('wp_footer', array($this, 'js'));
		}

		public function scripts() { wp_enqueue_script('jquery'); }

		public function style() {
			$btn = $this->_buttonClass; ?>
			<style type="text/css">
				html {
					margin-top:0 !important;
				}

				body.admin-bar:before {
				   top:0 !important;
				}

				#wpadminbar .<?php echo $btn; ?> {
					position:absolute !important;
					right:0 !important;
					width:46px !important;
					height:32px !important;
					cursor:pointer !important;
					text-align:center !important;
				}

				#wpadminbar .<?php echo $btn; ?> span.ab-icon {
					float:none !important;
					font-size:20px !important;
					line-height:30px !important;
					padding:0 !important;
					margin-right:0 !important;
				}

				#wpadminbar .<?php echo $btn; ?> span.ab-icon:before {
					content:"\f347";
					color:#fff !important;
					display:inline-block !important;
				}
			</style>
		<?php }

		public function js() { ?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					var $bar = $('#wpadminbar');
					var hiddenClass = '<?php echo $this->_hiddenClass; ?>';
					var buttonClass = '<?php echo $this->_buttonClass; ?>';

					$bar.css({'top': -barHeight()}).addClass(hiddenClass);

					$bar.append($('<div class="' + buttonClass + '"><span class="ab-icon"></span></div>').css({
						'background-color': $bar.css('background-color')
					}));

					var $button = $('.' + buttonClass);

					function barHeight() {
						return $bar.outerHeight();
					}

					function barHidden() {
						return $bar.hasClass(hiddenClass);
					}

					function wtbOffset() {
						$button.css({
							'top': barHeight()
						});
					}

					(function() {
						var speed = 300;

						$button.on('click', function() {
							if(barHidden()) {
								$bar.stop().animate(
									{'top': 0},
									speed,
									function() {
										$(this).toggleClass(hiddenClass);
									}
								);
							} else {
								$bar.stop().animate(
									{'top': -barHeight()},
									speed,
									function() {
										$(this).toggleClass(hiddenClass);
									}
								);
							}
						});
					})();

					wtbOffset();

					$(window).on('resize', function() {
						if(barHidden()) {
							$bar.css({'top': -barHeight()});
						} else {
							$bar.css({'top': 0});
						}

						wtbOffset();
					});
				});
			</script>
		<?php }
	}
}

add_action('after_setup_theme', function() {
	if(!is_admin() && is_admin_bar_showing()) {
		$wolfen_toggle_bar = new Wolfen_Toggle_Bar;
	}
});