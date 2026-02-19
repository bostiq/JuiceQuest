<?php

/**
 * Display post excerpt if available
 */
function jq_display_post_excerpt($post_id) {
	$excerpt_content = '';
	
	// Validate and sanitize post ID
	$post_id = absint($post_id);
	if (!$post_id) {
		return '';
	}
	
	// Get the excerpt
	$excerpt = get_the_excerpt($post_id);
	
	if (!empty($excerpt)) {
		$excerpt_content .= '<div class="post-excerpt-container">';
		$excerpt_content .= '<div class="excerpt-content">' . wp_kses_post($excerpt) . '</div>';
		$excerpt_content .= '</div>';
	}
	
	return $excerpt_content;
}

/**
 * Extract and display video metadata from WordPress attachment data
 */
function jq_display_video_metadata($video_content, $post_id) {
	$metadata = '';
	
	// Safety check and sanitization
	$post_id = absint($post_id);
	if (!$post_id) {
		return '';
	}
	
	// Sanitize video content parameter
	$video_content = wp_kses_post($video_content);
	
	// TOGGLE CONTROLS - Set to false to hide specific metadata
	$show_filename = false;
	$show_filetype = true;  
	$show_filesize = true;
	$show_duration = true;
	$show_dimensions = true;
	$show_audio_format = false;
	$show_audio_codec = false;
	$show_debug = false; // Set to true to enable debug info
	// END TOGGLE CONTROLS
	
	// Find video attachments for this post
	$video_attachments = jq_get_post_video_attachments($post_id);
	
	// Debug information
	if ($show_debug) {
		$metadata .= '<div style="background: #fffbcc; padding: 10px; margin: 10px 0; border: 1px solid #ddd;">'
			. '<strong>Debug Info:</strong><br>'
			. 'Post ID: ' . esc_html($post_id) . '<br>'
			. 'Video attachments found: ' . esc_html(count($video_attachments)) . '<br>'
			. 'Attachment IDs: ' . esc_html(implode(', ', array_map('absint', $video_attachments))) . '<br>';
			
		// Show what content we're looking at
		$post_content = get_post_field('post_content', $post_id);
		$metadata .= 'Post content length: ' . esc_html(strlen($post_content)) . ' characters<br>';
		$metadata .= 'Has [video] shortcode: ' . (strpos($post_content, '[video') !== false ? 'Yes' : 'No') . '<br>';
		$metadata .= 'Has video file references: ' . (preg_match('/\.(mp4|webm|ogg|avi|mov|wmv|flv|mkv)/i', $post_content) ? 'Yes' : 'No') . '<br>';
		$metadata .= '</div>';
	}
	
	if (!empty($video_attachments)) {
		$metadata .= '<div class="video-metadata-container" style="background: #f8f8f8; padding: 15px; margin: 20px 0; border-radius: 5px; border-left: 4px solid #333;">';
		$metadata .= '<h4 style="margin: 0 0 10px 0; font-size: 16px; color: #333;">Video Information</h4>';
		
		foreach ($video_attachments as $attachment_id) {
			$attachment_meta = wp_get_attachment_metadata($attachment_id);
			$attachment = get_post($attachment_id);
			
			$metadata .= '<div class="video-meta-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-bottom: 15px;">';
			
			// File name
			if ($show_filename && $attachment) {
				$filename = basename(get_attached_file($attachment_id));
				$metadata .= '<div class="meta-item"><strong>File name:</strong> ' . esc_html($filename) . '</div>';
			}
			
			// File type
			if ($show_filetype && $attachment) {
				$filetype = strtoupper(pathinfo($attachment->post_title, PATHINFO_EXTENSION));
				if (empty($filetype)) {
					$filetype = strtoupper(get_post_mime_type($attachment_id));
					$filetype = str_replace('video/', '', $filetype);
				}
				$metadata .= '<div class="meta-item"><strong>File type:</strong> ' . esc_html($filetype) . '</div>';
			}
			
			// File size
			if ($show_filesize) {
				$file_path = get_attached_file($attachment_id);
				if (file_exists($file_path)) {
					$file_size = filesize($file_path);
					$file_size_formatted = jq_format_file_size($file_size);
					$metadata .= '<div class="meta-item"><strong>File size:</strong> ' . esc_html($file_size_formatted) . '</div>';
				}
			}
			
			// Duration/Length
			if ($show_duration && !empty($attachment_meta['length_formatted'])) {
				$metadata .= '<div class="meta-item"><strong>Length:</strong> ' . esc_html($attachment_meta['length_formatted']) . '</div>';
			} elseif ($show_duration && !empty($attachment_meta['length'])) {
				$duration_formatted = jq_format_duration($attachment_meta['length']);
				$metadata .= '<div class="meta-item"><strong>Length:</strong> ' . esc_html($duration_formatted) . '</div>';
			}
			
			// Dimensions
			if ($show_dimensions && !empty($attachment_meta['width']) && !empty($attachment_meta['height'])) {
				$metadata .= '<div class="meta-item"><strong>Dimensions:</strong> ' . esc_html($attachment_meta['width'] . ' by ' . $attachment_meta['height'] . ' pixels') . '</div>';
			}
			
			// Audio Format 
			if ($show_audio_format && !empty($attachment_meta['audio'])) {
				$audio_format = !empty($attachment_meta['audio']['format']) ? $attachment_meta['audio']['format'] : 'mp4';
				$metadata .= '<div class="meta-item"><strong>Audio Format:</strong> ' . esc_html($audio_format) . '</div>';
			}
			
			// Audio Codec
			if ($show_audio_codec && !empty($attachment_meta['audio'])) {
				if (!empty($attachment_meta['audio']['codec'])) {
					$metadata .= '<div class="meta-item"><strong>Audio Codec:</strong> ' . esc_html($attachment_meta['audio']['codec']) . '</div>';
				} elseif (!empty($attachment_meta['audio']['format'])) {
					// Fallback to format if codec not available
					$codec = $attachment_meta['audio']['format'] === 'mp4' ? 'ISO/IEC 14496-3 AAC' : strtoupper($attachment_meta['audio']['format']);
					$metadata .= '<div class="meta-item"><strong>Audio Codec:</strong> ' . esc_html($codec) . '</div>';
				}
			}
			
			$metadata .= '</div>';
		}
		
		$metadata .= '</div>';
	} else {
		// If debug is enabled and no videos found, still show debug info
		if ($show_debug) {
			$metadata .= '<div style="background: #ffcccc; padding: 10px; margin: 10px 0; border: 1px solid #ddd;">'
				. '<strong>No video attachments found</strong><br>'
				. 'Try uploading videos directly to this post or check if videos are properly embedded.'
				. '</div>';
		}
	}
	
	return $metadata;
}

/**
 * Get video attachments for a specific post
 */
function jq_get_post_video_attachments($post_id) {
	$video_attachments = array();
	
	// Validate and sanitize post ID
	$post_id = absint($post_id);
	if (!$post_id) {
		return $video_attachments;
	}
	
	try {
		// Method 1: Get attachments directly attached to this post
		$attachments = get_children(array(
			'post_parent' => $post_id,
			'post_type' => 'attachment',
			'post_mime_type' => 'video',
			'posts_per_page' => -1,
			'post_status' => 'inherit'
		));
		
		if (!empty($attachments) && is_array($attachments)) {
			foreach ($attachments as $attachment) {
				$attachment_id = absint($attachment->ID);
				if ($attachment_id) {
					$video_attachments[] = $attachment_id;
				}
			}
		}
		
		// Method 2: If no attachments found, look for videos referenced in post content
		if (empty($video_attachments)) {
			$post_content = get_post_field('post_content', $post_id);
			
			// Sanitize post content before processing
			$post_content = wp_kses_post($post_content);
		
		// Look for WordPress video shortcodes - limit to reasonable patterns
		if (preg_match_all('/\[video[^\]]{0,500}\]/i', $post_content, $video_shortcodes)) {
			foreach ($video_shortcodes[0] as $shortcode) {
				// More restrictive pattern to prevent issues
				if (preg_match('/wp-content\/uploads\/[\d\/]{4,20}([^"\s\]]{1,100}\.(mp4|webm|ogg|avi|mov|wmv|flv|mkv))/i', $shortcode, $filename_match)) {
					$attachment_id = jq_get_attachment_id_by_filename(sanitize_file_name($filename_match[1]));
					if ($attachment_id) {
						$video_attachments[] = absint($attachment_id);
					}
				}
			}
		}
		
		// Look for video file references in content with stricter validation
		if (preg_match_all('/wp-content\/uploads\/[\d\/]{4,20}([^"\s]{1,100}\.(mp4|webm|ogg|avi|mov|wmv|flv|mkv))/i', $post_content, $video_files)) {
			foreach ($video_files[1] as $filename) {
				$safe_filename = sanitize_file_name(basename($filename));
				if (!empty($safe_filename)) {
					$attachment_id = jq_get_attachment_id_by_filename($safe_filename);
					$attachment_id = absint($attachment_id);
					if ($attachment_id && !in_array($attachment_id, $video_attachments)) {
						$video_attachments[] = $attachment_id;
					}
				}
			}
		}
	}
	
	} catch (Exception $e) {
		// Log error for debugging but don't expose details to users
		error_log('Video attachment error: ' . $e->getMessage());
		return array();
	}
	
	// Ensure all IDs are integers and remove duplicates
	$video_attachments = array_unique(array_map('absint', $video_attachments));
	$video_attachments = array_filter($video_attachments); // Remove any zeros
	
	return $video_attachments;
}

/**
 * Format file size in human readable format
 */
function jq_format_file_size($size_in_bytes) {
	if ($size_in_bytes >= 1073741824) {
		return number_format($size_in_bytes / 1073741824, 1) . ' GB';
	} elseif ($size_in_bytes >= 1048576) {
		return number_format($size_in_bytes / 1048576, 1) . ' MB';
	} elseif ($size_in_bytes >= 1024) {
		return number_format($size_in_bytes / 1024, 1) . ' KB';
	} else {
		return $size_in_bytes . ' bytes';
	}
}

/**
 * Format duration from seconds to human readable format
 */
function jq_format_duration($seconds) {
	$hours = floor($seconds / 3600);
	$minutes = floor(($seconds % 3600) / 60);
	$seconds = $seconds % 60;
	
	if ($hours > 0) {
		return sprintf('%d hours, %d minutes, %d seconds', $hours, $minutes, $seconds);
	} elseif ($minutes > 0) {
		return sprintf('%d minutes, %d seconds', $minutes, $seconds);
	} else {
		return sprintf('%d seconds', $seconds);
	}
}

/**
 * Get attachment ID by filename
 */
function jq_get_attachment_id_by_filename($filename) {
	global $wpdb;
	
	// Sanitize filename input
	$filename = sanitize_file_name($filename);
	if (empty($filename) || strlen($filename) > 255) {
		return false;
	}
	
	try {
		// Use WordPress prepared statement for security
		$attachment = $wpdb->get_col($wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type='attachment' AND (post_title=%s OR guid LIKE %s) LIMIT 5",
			pathinfo($filename, PATHINFO_FILENAME),
			'%' . $wpdb->esc_like($filename) . '%'
		));
		
		if (!empty($attachment) && is_array($attachment)) {
			return absint($attachment[0]);
		}
		
		return false;
	} catch (Exception $e) {
		// Log error for debugging
		error_log('Database query error in jq_get_attachment_id_by_filename: ' . $e->getMessage());
		return false;
	}
}

get_header();

$show_default_title = get_post_meta(get_the_ID(), '_et_pb_show_title', true);

$is_page_builder_used = et_pb_is_pagebuilder_used(get_the_ID());

?>

<div id="main-content">
	<?php
	if (et_builder_is_product_tour_enabled()):
		// load fullwidth page in Product Tour mode
		while (have_posts()): the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class('et_pb_post'); ?>>
				<div class="entry-content">
					<?php
					the_content();
					?>
				</div>

			</article>

		<?php endwhile;
	else:
		?>
		<div class="container">
			<div id="content-area" class="clearfix">
				<div id="left-area">
					<?php while (have_posts()) : the_post(); ?>
						<?php
						/**
						 * Fires before the title and post meta on single posts.
						 *
						 * @since 3.18.8
						 */
						do_action('et_before_post');
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class('et_pb_post'); ?>>
							<?php if (('off' !== $show_default_title && $is_page_builder_used) || ! $is_page_builder_used) { ?>
								<div class="et_post_meta_wrapper">
									<!-- 1. Title -->
									<h1 class="entry-title"><?php the_title(); ?></h1>

									<?php
									if (! post_password_required()) :
										
										// Setup thumbnail with 300px width
										$width = 300;
										$height = 0; // Set to 0 to maintain aspect ratio and prevent cropping
										$classtext = 'et_featured_image';
										$titletext = get_the_title();
										$alttext = get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true);
										$thumbnail = get_thumbnail($width, $height, $classtext, $alttext, $titletext, false, 'Blogimage');
										$thumb = $thumbnail["thumb"];

										$post_format = et_pb_post_format();
										$first_video = et_get_first_video();

										// 2. Small thumbnail (300 width) - Show for all posts if available
										if ('on' === et_get_option('divi_thumbnails', 'on') && '' !== $thumb && ! in_array($post_format, array('gallery', 'link', 'quote'))) {
											print_thumbnail($thumb, $thumbnail["use_timthumb"], $alttext, $width, $height);
										}

										// 3. Excerpt
										echo jq_display_post_excerpt(get_the_ID());

										// 4. Post meta
										et_divi_post_meta();

										// 5. Video - Show video player for video format, or handle other formats
										if ('video' === $post_format && false !== $first_video) {
											printf(
												'<div class="et_main_video_container">
											%1$s
										</div>',
												et_core_esc_previously($first_video)
											);
											// 6. Video meta - Show immediately after video for video format posts
											echo jq_display_video_metadata('', get_the_ID());
										} else if ('gallery' === $post_format) {
											et_pb_gallery_images();
										}

										// Note: For standard posts, video metadata will show after main content

										$text_color_class = et_divi_get_post_text_color();
										$inline_style = et_divi_get_post_bg_inline_style();

										switch ($post_format) {
											case 'audio':
												$audio_player = et_pb_get_audio_player();

												if ($audio_player) {
													printf(
														'<div class="et_audio_content%1$s"%2$s>
													%3$s
												</div>',
														esc_attr($text_color_class),
														et_core_esc_previously($inline_style),
														et_core_esc_previously($audio_player)
													);
												}

												break;
											case 'quote':
												printf(
													'<div class="et_quote_content%2$s"%3$s>
												%1$s
											</div>',
													et_core_esc_previously(et_get_blockquote_in_content()),
													esc_attr($text_color_class),
													et_core_esc_previously($inline_style)
												);

												break;
											case 'link':
												printf(
													'<div class="et_link_content%3$s"%4$s>
												<a href="%1$s" class="et_link_main_url">%2$s</a>
											</div>',
													esc_url(et_get_link_url()),
													esc_html(et_get_link_url()),
													esc_attr($text_color_class),
													et_core_esc_previously($inline_style)
												);

												break;
										}

									endif;
									?>
								</div>
							<?php  } ?>

							<div class="entry-content">
								<?php
								do_action('et_before_content');

								the_content();

								wp_link_pages(array('before' => '<div class="page-links">' . esc_html__('Pages:', 'Divi'), 'after' => '</div>'));
								?>
							</div>
							<?php
							// Show video metadata after main content for non-video format posts
							$current_post_format = et_pb_post_format();
							if ('video' !== $current_post_format) {
								echo jq_display_video_metadata('', get_the_ID());
							}
							?>
							<div class="et_post_meta_wrapper">
								<?php
								if (et_get_option('divi_468_enable') === 'on') {
									echo '<div class="et-single-post-ad">';
									if (et_get_option('divi_468_adsense') !== '') echo et_core_intentionally_unescaped(et_core_fix_unclosed_html_tags(et_get_option('divi_468_adsense')), 'html');
									else { ?>
										<a href="<?php echo esc_url(strval(et_get_option('divi_468_url'))); ?>"><img src="<?php echo esc_attr(et_get_option('divi_468_image')); ?>" alt="468" class="foursixeight" /></a>
								<?php 	}
									echo '</div>';
								}
								?>
								
								<?php

								/**
								 * Fires after the post content on single posts.
								 *
								 * @since 3.18.8
								 */
								do_action('et_after_post');

								if ((comments_open() || get_comments_number()) && 'on' === et_get_option('divi_show_postcomments', 'on')) {
									comments_template('', true);
								}
								?>
							</div>
						</article>

					<?php endwhile; ?>
				</div>

				<?php get_sidebar(); ?>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php

get_footer();
