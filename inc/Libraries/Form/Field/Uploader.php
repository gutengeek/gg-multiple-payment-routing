<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    ggmp
 * @author     Opal  Team <info@ggmp.com >
 * @copyright  Copyright (C) 2019 ggmp.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.ggmp.com
 * @support  http://www.ggmp.com/support/forum.html
 */
namespace GGMP\Libraries\Form\Field;

use GGMP\Libraries\Form\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Uploader
 */
class Uploader {

	/**
	 * Constructor
	 */
	public function __construct( $args, Form $form ) {

		$defaults = [];
		$args     = wp_parse_args( $args, $defaults );

		$this->args = $args;
		$this->form = $form;
		$this->render();
	}

	/**
	 * Register javascript file for processing upload images/files
	 */
	public function scripts_styles() {
		wp_register_script(
			'ggmp-uploader',
			OPALESTATE_PLUGIN_URL . 'assets/js/frontend/uploader.js',
			[
				'jquery',
			],
			'4.4.3',
			true
		);
	}

	/**
	 * Render Preview is image or icon with its name
	 */
	private function render_image_or_icon( $escaped_value, $show_icon ) {
		$cls = $show_icon ? "preview-icon" : "preview-image";
		echo '<div class="inner ' . $cls . '">';
		echo '      <span class="btn-close"></span> ';
		if ( $show_icon ) {
			echo '<i class="fas fa-paperclip"></i> ' . basename( get_attached_file( $escaped_value ) );
		} else {
			echo wp_get_attachment_image( $escaped_value, 'thumbnail' );
		}

		echo '</div>';
	}

	/**
	 * Render content input field.
	 */
	public function render() {
		$args          = $this->args;
		$escaped_value = $this->form->get_field_value( $args );

		$this->form->add_dependencies( 'ggmp-uploader-js' );

		$field_name = $args['id'];

		$args = [
			'type'  => 'checkbox',
			'id'    => $field_name,
			'name'  => $field_name,
			'desc'  => '',
			'value' => 'on',
		];

		if ( $escaped_value == 'on' || $escaped_value == 1 ) {
			$args['checked'] = 'checked';
		}

		$single = isset( $args['single'] ) && $args['single'];
		$attrs  = $single ? "" : 'multiple="multiple"';
		$size   = '';


		if ( isset( $args['accept'] ) && $args['accept'] ) {
			$attrs .= ' accept="' . $args['accept'] . '" ';


			$info = [
				'size'   => ggmp_get_option( 'upload_other_max_size', 0.5 ),
				'number' => ggmp_get_option( 'upload_other_max_files', 10 ),
			];

			$class = 'upload-file-wrap';
		} else {
			$attrs .= ' accept="image/*"  ';
			$class = 'upload-image-wrap';

			$info = [
				'size'   => ggmp_get_option( 'upload_image_max_size', 0.5 ),
				'number' => ggmp_get_option( 'upload_image_max_files', 10 ),
			];
		}
		if ( $single ) {
			$info['number'] = 1;
		}
		$show_icon = isset( $args['show_icon'] ) && $args['show_icon'] ? $args['show_icon'] : false;
		?>
        <div class="ggmp-uploader-files <?php echo $class; ?>" data-name="<?php echo $args['id']; ?>" data-single="<?php echo $single; ?>" data-show-icon="<?php echo $show_icon; ?>">
			<?php if ( $escaped_value && is_array( $escaped_value ) ): ?>
				<?php foreach ( $escaped_value as $key => $url ): ?>
                    <div class="uploader-item-preview">

						<?php echo $this->render_image_or_icon( $key, $show_icon ); ?>
                        <input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $key; ?>">
                    </div>
				<?php endforeach; ?>
			<?php elseif ( $escaped_value && ! is_array( $escaped_value ) ): ?>
                <div class="uploader-item-preview">

					<?php echo $this->render_image_or_icon( $escaped_value, $show_icon ); ?>

                    <input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $escaped_value; ?>">
                </div>
			<?php elseif ( empty( $escaped_value ) && isset( $args['value'] ) && (int) $args['value'] ):
				$image_id = $args['value'];
				?>
                <div class="uploader-item-preview">

					<?php echo $this->render_image_or_icon( $image_id, $show_icon ); ?>
                    <input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $image_id; ?>">
                </div>
			<?php endif; ?>
            <div class="button-placehold">
                <div class="button-placehold-content">
                    <i class="fa fa-plus"></i>
                    <span><?php _e( "Upload", "ggmp" ); ?></span>
                </div>
            </div>
            <input type="file" name="<?php echo $args['id']; ?>" <?php echo $attrs; ?> class="select-file" style="visibility: hidden;">


        </div>
        <p class="ggmp-metabox-description">
            <i>
				<?php
				echo sprintf( __( 'Allow upload file have size < %s MB and maximum number of files: %s', 'ggmp' ),
					'<strong>' . $info['size'] . '</strong>', '<strong>' . $info['number'] . '</strong>' ); ?>

            </i>
        </p>
		<?php
	}

	/**
	 *
	 */
	public function admin_head() {
		?>


		<?php
	}
}

