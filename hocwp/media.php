<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}
function hocwp_media_sanitize_upload_file_name( $file ) {
	$file_name    = isset( $file['name'] ) ? $file['name'] : '';
	$file['name'] = hocwp_sanitize_file_name( $file_name );

	return $file;
}

add_filter( 'wp_handle_upload_prefilter', 'hocwp_media_sanitize_upload_file_name' );

function hocwp_get_media_file_path( $media_id ) {
	return get_attached_file( $media_id );
}

function hocwp_crop_image_helper( $args = array() ) {
	$source      = hocwp_get_value_by_key( $args, 'source' );
	$dest        = hocwp_get_value_by_key( $args, 'dest' );
	$width       = hocwp_get_value_by_key( $args, 'width' );
	$height      = hocwp_get_value_by_key( $args, 'height' );
	$crop_center = (bool) hocwp_get_value_by_key( $args, 'crop_center' );
	$x           = absint( hocwp_get_value_by_key( $args, 'x', 0 ) );
	$y           = absint( hocwp_get_value_by_key( $args, 'y', 0 ) );
	$info        = pathinfo( $source );
	$extension   = hocwp_get_value_by_key( $info, 'extension', 'jpg' );
	$image       = null;
	switch ( $extension ) {
		case 'png':
			$image = imagecreatefrompng( $source );
			break;
		case 'gif':
			$image = imagecreatefromgif( $source );
			break;
		case 'jpeg':
		case 'jpg':
			$image = imagecreatefromjpeg( $source );
			break;
	}
	if ( null === $image ) {
		return $dest;
	}
	$thumb_width     = $width;
	$thumb_height    = $height;
	$width           = imagesx( $image );
	$height          = imagesy( $image );
	$original_aspect = $width / $height;
	$thumb_aspect    = $thumb_width / $thumb_height;
	if ( $original_aspect >= $thumb_aspect ) {
		$new_width  = $width / ( $height / $thumb_height );
		$new_height = $thumb_height;
	} else {
		$new_width  = $thumb_width;
		$new_height = $height / ( $width / $thumb_width );
	}
	$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
	if ( $crop_center ) {
		$x = 0 - ( $new_width - $thumb_width ) / 2;
		$y = 0 - ( $new_height - $thumb_height ) / 2;
	}
	imagecopyresampled( $thumb, $image, $x, $y, 0, 0, $new_width, $new_height, $width, $height );
	$quality = absint( apply_filters( 'hocwp_image_quality', 80 ) );
	if ( ! is_numeric( $quality ) || $quality < 0 || $quality > 100 ) {
		$quality = 80;
	}
	switch ( $extension ) {
		case 'png':
			$first_char = hocwp_get_first_char( $quality );
			$quality    = absint( $first_char );
			imagepng( $thumb, $dest, $quality );
			break;
		case 'gif':
			imagegif( $thumb, $dest );
			break;
		case 'jpeg':
		case 'jpg':
			imagejpeg( $thumb, $dest, $quality );
			break;
	}
	unset( $image );
	unset( $thumb );

	return $dest;
}

function hocwp_crop_image( $args = array() ) {
	$attachment_id = hocwp_get_value_by_key( $args, 'attachment_id' );
	$url           = hocwp_get_value_by_key( $args, 'url' );
	$base_url      = '';
	if ( ! hocwp_id_number_valid( $attachment_id ) && ! empty( $url ) ) {
		$attachment_id = hocwp_get_media_id( $url );
	}
	if ( ! hocwp_id_number_valid( $attachment_id ) ) {
		if ( empty( $url ) ) {
			return new WP_Error( 'crop_image_size', __( 'Attachment ID is not valid.', 'hocwp-theme' ) );
		} else {
			$cropped = $url;
		}
	} else {
		$file_path = hocwp_get_media_file_path( $attachment_id );
		$width     = hocwp_get_value_by_key( $args, 'width' );
		$height    = hocwp_get_value_by_key( $args, 'height' );
		$size      = hocwp_get_image_sizes( $attachment_id );
		$size      = hocwp_sanitize_size( $size );
		if ( empty( $width ) && empty( $height ) ) {
			$cropped = $file_path;
		} else {
			if ( empty( $width ) ) {
				$width = $size[0];
			}
			if ( empty( $height ) ) {
				$height = $size[1];
			}
			$x         = apply_filters( 'hocwp_crop_image_x', 0, $args );
			$y         = apply_filters( 'hocwp_crop_image_y', 0, $args );
			$x         = hocwp_get_value_by_key( $args, 'x', $x );
			$y         = hocwp_get_value_by_key( $args, 'y', $y );
			$dest_file = hocwp_get_value_by_key( $args, 'dest_file', '' );
			$path_info = pathinfo( $file_path );
			if ( empty( $dest_file ) ) {
				$upload_dir = hocwp_get_upload_folder_details();
				$base_path  = apply_filters( 'hocwp_custom_thumbnail_base_path', untrailingslashit( $upload_dir['path'] ) . '/hocwp/thumbs/', $args );
				if ( ! file_exists( $base_path ) ) {
					wp_mkdir_p( $base_path );
				}
				$base_url  = apply_filters( 'hocwp_custom_thumbnail_base_url', untrailingslashit( $upload_dir['url'] ) . '/hocwp/thumbs/', $args );
				$filename  = $path_info['filename'];
				$dest_file = $base_path . str_replace( $filename, $filename . '-' . $width . '-' . $height, basename( $file_path ) );
			}
			$crop_args = array(
				'source' => get_attached_file( $attachment_id ),
				'dest'   => $dest_file,
				'width'  => $width,
				'height' => $height,
				'x'      => $x,
				'y'      => $y
			);
			$crop_args = wp_parse_args( $args, $crop_args );
			if ( file_exists( $dest_file ) ) {
				$override = hocwp_get_value_by_key( $args, 'override', false );
				if ( $override ) {
					unlink( $dest_file );
					$cropped = hocwp_crop_image_helper( $crop_args );
				} else {
					$cropped = $dest_file;
				}
			} else {
				$cropped = hocwp_crop_image_helper( $crop_args );
			}
		}
	}
	if ( file_exists( $cropped ) ) {
		$output = hocwp_get_value_by_key( $args, 'output', 'url' );
		if ( 'url' == $output ) {
			$cropped = hocwp_media_path_to_url( $attachment_id, $cropped, $base_url );
		}
	} else {
		$cropped = $url;
	}

	return apply_filters( 'hocwp_crop_image', $cropped, $args );
}

function hocwp_media_path_to_url( $attachment_id, $file_path, $base_url = '' ) {
	if ( empty( $base_url ) ) {
		$parent_url = wp_get_attachment_url( $attachment_id );
		$url        = str_replace( basename( $parent_url ), basename( $file_path ), $parent_url );
	} else {
		$url = trailingslashit( $base_url ) . basename( $file_path );
	}

	return apply_filters( 'hocwp_media_path_to_url', $url, $attachment_id, $file_path );
}

function hocwp_post_thumbnail_by_ajax( $url, $thumbnail_url, $params ) {
	if ( HOCWP_DOING_AJAX ) {
		$params['url']            = $thumbnail_url;
		$params['ajax_thumbnail'] = true;
		$params['crop_center']    = true;
		$params['override']       = true;
		$url                      = hocwp_crop_image( $params );
	}

	return $url;
}

add_filter( 'hocwp_pre_bfi_thumb', 'hocwp_post_thumbnail_by_ajax', 10, 3 );

function hocwp_media_mime_type_icon( $icon, $mime ) {
	$change_icon = apply_filters( 'hocwp_use_custom_mime_type_icon', false );
	if ( $change_icon ) {
		switch ( $mime ) {
			case 'application/pdf':
				$icon = hocwp_get_image_url( 'mime-types/icon-pdf.png' );
				break;
			case 'application/vnd.ms-word.document.macroEnabled.12':
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.template':
			case 'application/vnd.ms-word.template.macroEnabled.12':
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
			case 'application/msword':
				$icon = hocwp_get_image_url( 'mime-types/icon-doc.png' );
				break;
			case 'application/rar':
				$icon = hocwp_get_image_url( 'mime-types/icon-rar.png' );
				break;
			case 'application/zip':
				$icon = hocwp_get_image_url( 'mime-types/icon-zip.png' );
				break;
			case 'application/x-gzip':
				$icon = hocwp_get_image_url( 'mime-types/icon-gz.png' );
				break;
			case 'application/x-7z-compressed':
				$icon = hocwp_get_image_url( 'mime-types/icon-7z.png' );
				break;
			case 'application/vnd.ms-excel.addin.macroEnabled.12':
			case 'application/vnd.ms-excel.template.macroEnabled.12':
			case 'application/vnd.ms-excel.sheet.binary.macroEnabled.12':
			case 'application/vnd.ms-excel.sheet.macroEnabled.12':
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
			case 'application/vnd.ms-excel':
				$icon = hocwp_get_image_url( 'mime-types/icon-xls.png' );
				break;
		}
	}

	return $icon;
}

add_filter( 'wp_mime_type_icon', 'hocwp_media_mime_type_icon', 10, 2 );