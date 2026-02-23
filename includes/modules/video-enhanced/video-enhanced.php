<?php

class ET_Builder_Module_Video_Enhanced extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Video Enhanced', 'et_builder' );
		$this->plural     = esc_html__( 'Videos Enhanced', 'et_builder' );
		$this->slug       = 'et_pb_video_enhanced';
		$this->vb_support = 'on';
		
		// Module icon and category
		$this->icon_path = false;
		$this->icon = '&#xE023;'; // Play button icon
		$this->main_css_element = '%%order_class%%';
		$this->type = 'general';
		$this->child_title_var = 'admin_label';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Video', 'et_builder' ),
					'overlay'      => esc_html__( 'Overlay', 'et_builder' ),
					'video_meta'   => esc_html__( 'Video Meta', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'play_icon' => esc_html__( 'Play Icon', 'et_builder' ),
				),
			),
		);

		$this->custom_css_fields = array(
			'video_icon' => array(
				'label'    => esc_html__( 'Video Icon', 'et_builder' ),
				'selector' => '.et_pb_video_play',
			),
		);

		$this->advanced_fields = array(
			'background'            => array(
				'options' => array(
					'background_color' => array(
						'depends_on'      => array(
							'custom_padding',
						),
						'depends_on_responsive' => array(
							'custom_padding',
						),
						'depends_show_if_not' => array(
							'',
							'|||',
						),
						'is_toggleable' => true,
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'overlay' => 'inset',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
				'custom_padding' => array(
					'responsive_affects' => array(
						'background_color',
					),
				),
			),
			'fonts'                 => false,
			'text'                  => false,
			'button'                => false,
			'link_options'          => false,
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( '3jXN8CBz0TU' ),
				'name' => esc_html__( 'An introduction to the Video module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'src' => array(
				'label'              => esc_html__( 'Video MP4/URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video MP4 File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired video in .MP4 format, or type in the URL to the video you would like to display', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'computed_affects' => array(
					'__video',
				),
			),
			'src_webm' => array(
				'label'              => esc_html__( 'Video Webm', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload the .WEBM version of your video here. All uploaded videos should be in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers.', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'computed_affects' => array(
					'__video',
				),
			),
			'image_src' => array(
				'label'              => esc_html__( 'Image Overlay URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'additional_button'  => sprintf(
					'<input type="button" class="button et-pb-video-image-button" value="%1$s" />',
					esc_attr__( 'Generate From Video', 'et_builder' )
				),
				'additional_button_type' => 'generate_image_url_from_video',
				'additional_button_attrs' => array(
					'video_source' => 'src',
				),
				'classes'            => 'et_pb_video_overlay',
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display over your video. You can also generate a still image from your video.', 'et_builder' ),
				'toggle_slug'        => 'overlay',
				'computed_affects' => array(
					'__video_cover_src',
				),
				'dynamic_content'   => 'image',
			),
			'play_icon_color' => array(
				'label'             => esc_html__( 'Play Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'play_icon',
				'hover'             => 'tabs',
			),
			'__video' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Video_Enhanced', 'get_video' ),
				'computed_depends_on' => array(
					'src',
					'src_webm',
				),
				'computed_minimum' => array(
					'src',
					'src_webm',
				),
			),
			'__video_cover_src' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Video_Enhanced', 'get_video_cover_src' ),
				'computed_depends_on' => array(
					'image_src',
				),
				'computed_minimum' => array(
					'image_src',
				),
			),
			'show_duration' => array(
				'label'           => esc_html__( 'Show Video Duration', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'         => 'off',
				'toggle_slug'     => 'video_meta',
				'description'     => esc_html__( 'Display the duration of the video file.', 'et_builder' ),
			),
			'show_file_type' => array(
				'label'           => esc_html__( 'Show File Type', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'         => 'off',
				'toggle_slug'     => 'video_meta',
				'description'     => esc_html__( 'Display the file type/format of the video.', 'et_builder' ),
			),
			'show_file_size' => array(
				'label'           => esc_html__( 'Show File Size', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'         => 'off',
				'toggle_slug'     => 'video_meta',
				'description'     => esc_html__( 'Display the file size of the video.', 'et_builder' ),
			),
			'show_dimensions' => array(
				'label'           => esc_html__( 'Show File Dimensions', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'         => 'off',
				'toggle_slug'     => 'video_meta',
				'description'     => esc_html__( 'Display the dimensions (width x height) of the video in pixels.', 'et_builder' ),
			),
			'show_file_name' => array(
				'label'           => esc_html__( 'Show File Name', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'         => 'off',
				'toggle_slug'     => 'video_meta',
				'description'     => esc_html__( 'Display the file name of the video.', 'et_builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),

		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['play_icon_color'] = array( 'color' => '%%order_class%% .et_pb_video_overlay .et_pb_video_play' );

		return $fields;
	}

	/**
	 * Get video metadata from file
	 */
	static function get_video_metadata( $video_url ) {
		$metadata = array(
			'duration'   => '',
			'file_type'  => '',
			'file_size'  => '',
			'dimensions' => '',
			'file_name'  => '',
		);

		if ( empty( $video_url ) ) {
			return $metadata;
		}

		// Check if it's a local file
		if ( filter_var( $video_url, FILTER_VALIDATE_URL ) && strpos( $video_url, home_url() ) !== false ) {
			// Convert URL to local path
			$upload_dir = wp_upload_dir();
			$file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $video_url );
			
			if ( file_exists( $file_path ) ) {
				// Get file size
				$file_size = filesize( $file_path );
				$metadata['file_size'] = self::format_file_size( $file_size );
				
				// Get file type and name
				$file_info = pathinfo( $file_path );
				$metadata['file_type'] = strtoupper( $file_info['extension'] ?? '' );
				$metadata['file_name'] = $file_info['basename'] ?? '';
				
				// Try to get video metadata using getID3 if available
				if ( class_exists( 'getID3' ) ) {
					$getID3 = new getID3();
					$video_info = $getID3->analyze( $file_path );
					
					if ( isset( $video_info['playtime_seconds'] ) ) {
						$metadata['duration'] = self::format_duration( $video_info['playtime_seconds'] );
					}
					
					if ( isset( $video_info['video']['resolution_x'] ) && isset( $video_info['video']['resolution_y'] ) ) {
						$metadata['dimensions'] = $video_info['video']['resolution_x'] . ' x ' . $video_info['video']['resolution_y'] . ' pixels';
					}
				} else {
					// Fallback: try to get video info using WordPress media functions
					$attachment_id = attachment_url_to_postid( $video_url );
					if ( $attachment_id ) {
						$video_meta = wp_get_attachment_metadata( $attachment_id );
						if ( isset( $video_meta['length'] ) ) {
							$metadata['duration'] = self::format_duration( $video_meta['length'] );
						}
						if ( isset( $video_meta['width'] ) && isset( $video_meta['height'] ) ) {
							$metadata['dimensions'] = $video_meta['width'] . ' x ' . $video_meta['height'] . ' pixels';
						}
					}
				}
			}
		} else {
			// For external URLs, try to get file type and name from URL
			$path_info = pathinfo( parse_url( $video_url, PHP_URL_PATH ) );
			$metadata['file_type'] = strtoupper( $path_info['extension'] ?? 'URL' );
			$metadata['file_name'] = $path_info['basename'] ?? '';
		}

		return $metadata;
	}

	/**
	 * Format file size in human readable format
	 */
	static function format_file_size( $bytes ) {
		if ( $bytes <= 0 ) return '0 B';
		
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		$i = floor( log( $bytes, 1024 ) );
		
		return round( $bytes / pow( 1024, $i ), 2 ) . ' ' . $units[ $i ];
	}

	/**
	 * Format duration in human readable format
	 */
	static function format_duration( $seconds ) {
		if ( $seconds <= 0 ) return '0:00';
		
		$hours = floor( $seconds / 3600 );
		$minutes = floor( ( $seconds % 3600 ) / 60 );
		$seconds = $seconds % 60;
		
		if ( $hours > 0 ) {
			return sprintf( '%d:%02d:%02d', $hours, $minutes, $seconds );
		} else {
			return sprintf( '%d:%02d', $minutes, $seconds );
		}
	}

	static function get_video( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'src'      => '',
			'src_webm' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$video_src = '';

		if ( false !== et_pb_check_oembed_provider( esc_url( $args['src'] ) ) ) {
			$video_src = wp_oembed_get( esc_url( $args['src'] ) );
		} else {
			$video_src = sprintf( '
				<video controls>
					%1$s
					%2$s
				</video>',
				( '' !== $args['src'] ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $args['src'] ) ) : '' ),
				( '' !== $args['src_webm'] ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $args['src_webm'] ) ) : '' )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		return $video_src;
	}

	static function get_video_cover_src( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$post_id = isset( $current_page['id'] ) ? $current_page['id'] : self::get_current_post_id();
		$defaults = array(
			'image_src' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( isset( $args['image_src'] ) ) {
			$dynamic_value = et_builder_parse_dynamic_content( stripslashes( $args['image_src'] ) );
			if ( $dynamic_value->is_dynamic() && current_user_can( 'edit_post', $post_id ) ) {
				$args['image_src'] = $dynamic_value->resolve( $post_id );
			}
		}

		$image_output = '';

		if ( '' !== $args['image_src'] ) {
			$image_output = et_pb_set_video_oembed_thumbnail_resolution( $args['image_src'], 'high' );
		}

		return $image_output;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$src                   = $this->props['src'];
		$src_webm              = $this->props['src_webm'];
		$image_src             = $this->props['image_src'];
		$play_icon_color       = $this->props['play_icon_color'];
		$play_icon_color_hover = $this->get_hover_value( 'play_icon_color' );
		
		// Video metadata options - ensure defaults
		$show_duration    = isset( $this->props['show_duration'] ) ? $this->props['show_duration'] : 'off';
		$show_file_type   = isset( $this->props['show_file_type'] ) ? $this->props['show_file_type'] : 'off';
		$show_file_size   = isset( $this->props['show_file_size'] ) ? $this->props['show_file_size'] : 'off';
		$show_dimensions  = isset( $this->props['show_dimensions'] ) ? $this->props['show_dimensions'] : 'off';
		$show_file_name   = isset( $this->props['show_file_name'] ) ? $this->props['show_file_name'] : 'off';

		$video_src       = self::get_video( array(
			'src'      => $src,
			'src_webm' => $src_webm,
		) );

		$image_output = self::get_video_cover_src( array(
			'image_src' => $image_src,
		) );
		
		// Get video metadata if any meta options are enabled
		$video_metadata = '';
		if ( 'on' === $show_duration || 'on' === $show_file_type || 'on' === $show_file_size || 'on' === $show_dimensions || 'on' === $show_file_name ) {
			$primary_video_url = ! empty( $src ) ? $src : $src_webm;
			
			// Debug: Add a simple test first
			if ( ! empty( $primary_video_url ) ) {
				$metadata = self::get_video_metadata( $primary_video_url );
				
				$meta_items = array();

				if ( 'on' === $show_file_name ) {
					$file_name = ! empty( $metadata['file_name'] ) ? $metadata['file_name'] : basename( parse_url( $primary_video_url, PHP_URL_PATH ) );
					if ( ! empty( $file_name ) ) {
						$meta_items[] = '<span class="video-meta-filename"><strong>File Name:</strong> ' . esc_html( $file_name ) . '</span>';
					}
				}
				
				if ( 'on' === $show_duration && ! empty( $metadata['duration'] ) ) {
					$meta_items[] = '<span class="video-meta-duration"><strong>Duration:</strong> ' . esc_html( $metadata['duration'] ) . '</span>';
				}
				
				if ( 'on' === $show_file_type ) {
					$file_type = ! empty( $metadata['file_type'] ) ? $metadata['file_type'] : strtoupper( pathinfo( parse_url( $primary_video_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
					if ( ! empty( $file_type ) ) {
						$meta_items[] = '<span class="video-meta-type"><strong>Format:</strong> ' . esc_html( $file_type ) . '</span>';
					}
				}
				
				if ( 'on' === $show_file_size && ! empty( $metadata['file_size'] ) ) {
					$meta_items[] = '<span class="video-meta-size"><strong>Size:</strong> ' . esc_html( $metadata['file_size'] ) . '</span>';
				}
				
				if ( 'on' === $show_dimensions && ! empty( $metadata['dimensions'] ) ) {
					$meta_items[] = '<span class="video-meta-dimensions"><strong>Dimensions:</strong> ' . esc_html( $metadata['dimensions'] ) . '</span>';
				}
				
				if ( ! empty( $meta_items ) ) {
					$video_metadata = '<div class="et_pb_video_metadata" style="margin-top: 15px; padding: 10px; font-size: 14px; line-height: 1.6;">' . implode( '<br>', $meta_items ) . '</div>';
				}
			} else {
				// Show debug info if no video URL
				if ( 'on' === $show_file_name ) {
					$video_metadata = '<div class="et_pb_video_metadata" style="margin-top: 10px; padding: 10px; font-size: 14px; color: #cc0000;">No video URL provided for metadata extraction.</div>';
				}
			}
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		if ( '' !== $play_icon_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector' => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $play_icon_color )
				),
			) );
		}

		if ( et_builder_is_hover_enabled( 'play_icon_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector' => '%%order_class%% .et_pb_video_overlay .et_pb_video_play:hover',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $play_icon_color_hover )
				),
			) );
		}

		// Create a grid layout to properly separate video/overlay from metadata
		ET_Builder_Element::set_style( $render_slug, array(
			'selector' => '%%order_class%% .et_pb_module_inner',
			'declaration' => '
				display: grid;
				grid-template-rows: auto auto;
				gap: 0;
			',
		) );

		// Video box and overlay both go in the first grid cell
		ET_Builder_Element::set_style( $render_slug, array(
			'selector' => '%%order_class%% .et_pb_video_box, %%order_class%% .et_pb_video_overlay',
			'declaration' => '
				grid-row: 1;
				grid-column: 1;
			',
		) );

		// Video box needs relative positioning for the overlay
		ET_Builder_Element::set_style( $render_slug, array(
			'selector' => '%%order_class%% .et_pb_video_box',
			'declaration' => 'position: relative;',
		) );

		// Metadata goes in the second grid row
		ET_Builder_Element::set_style( $render_slug, array(
			'selector' => '%%order_class%% .et_pb_video_metadata',
			'declaration' => '
				grid-row: 2;
				grid-column: 1;
			',
		) );

		// Override default Divi video overlay positioning for our enhanced module
		ET_Builder_Element::set_style( $render_slug, array(
			'selector' => '%%order_class%% .et_pb_video_overlay, %%order_class%% .et_pb_video_overlay .et_pb_video_overlay_hover',
			'declaration' => '
				position: static;
				height: auto;
				width: auto;
				grid-row: 1;
				grid-column: 1;
			',
		) );

		$output = sprintf(
			'%1$s
			%2$s
			<div class="et_pb_video_box">
				%3$s
			</div>
			%4$s
			%5$s',
			$parallax_image_background,
			$video_background,
			( '' !== $video_src ? $video_src : '' ),
			( '' !== $image_output
				? sprintf(
					'<div class="et_pb_video_overlay" style="background-image: url(%1$s);">
						<div class="et_pb_video_overlay_hover">
							<a href="#" class="et_pb_video_play"></a>
						</div>
					</div>',
					esc_attr( $image_output )
				)
				: ''
			),
			$video_metadata
		);

		return $output;
	}
}

new ET_Builder_Module_Video_Enhanced;
