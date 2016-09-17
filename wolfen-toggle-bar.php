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

				#wpadminbar {
					position:fixed !important;
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
					var hiddenClass = '<?php echo $this->_hiddenClass; ?>';
					var buttonClass = '<?php echo $this->_buttonClass; ?>';

					var $bar = $('#wpadminbar');

					$bar.addClass(hiddenClass).append(
						$('<div class="' + buttonClass + '"></div>')
					).css({'visibility': 'hidden'});

					var $button = $('.' + buttonClass);

					$button.append('<span class="ab-icon"></span>').css({
						'background-color': $bar.css('background-color')
					});

					function barHeight() {
						return $bar.outerHeight();
					}

					function barHidden() {
						return $bar.hasClass(hiddenClass);
					}

					function suOffset() {
						var $su = $('#__su__toolbar');
						var topOffset = 0;

						if($su.length) {
							topOffset = parseInt($su.outerHeight());
						}

						return topOffset;
					}

					function wtbDo() {
						if(barHidden()) {
							$bar.css({'top': -barHeight() + suOffset()});
						} else {
							$bar.css({'top': suOffset()});
						}

						$button.css({
							'top': barHeight()
						});
					}

					function wtbInit() {
						var speed = 300;

						$button.on('click', function() {
							if(barHidden()) {
								$bar.stop().animate(
									{'top': suOffset()},
									speed,
									function() {
										$(this).toggleClass(hiddenClass);
									}
								);
							} else {
								$bar.stop().animate(
									{'top': -barHeight() + suOffset()},
									speed,
									function() {
										$(this).toggleClass(hiddenClass);
									}
								);
							}
						});

						$bar.css({'visibility': 'visible'});

						wtbDo();
					}

					$(window).load(wtbInit);
					$(window).resize(wtbDo);
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