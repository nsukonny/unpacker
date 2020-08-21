<?php
/**
 * Short method for functions.php
 * Image examples: https://d.pr/hCfNek and https://d.pr/MnerNb.
 *
 * 1. [custom_file_download_gz("ftp://rospapergirl1:asdf*1235!1@aftp.linksynergy.com/45658_3732636_mp.txt.gz")]
 *
 * @param $url
 *
 * @return mixed
 */
function custom_file_download_gz( $url ) {

	$is_ftp = 0 !== strpos( 'ftp://', $url );

	if ( $is_ftp ) {
		$result = file_get_contents( $url );
	} else {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "GET" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		$result = curl_exec( $ch );
		if ( curl_errno( $ch ) ) {
			exit( 'Error:' . curl_error( $ch ) );
		}
		curl_close( $ch );
	}

	$uploads = array( 'basedir' => __DIR__, 'baseurl' => 'https://caraudiodiscountdepot.com/wp-content/uploads' );
	if ( function_exists( 'wp_upload_dir' ) ) {
		$uploads = wp_upload_dir();
	}

	$filename = $uploads['basedir'] . '/' . strtok( basename( $url ), "?" );
	if ( file_exists( $filename ) ) {
		@unlink( $filename );
	}
	file_put_contents( $filename, $result );

	$buffer_size   = 4096;
	$out_file_name = str_replace( '.gz', '', $filename );

	$file     = gzopen( $filename, 'rb' );
	$out_file = fopen( $out_file_name, 'wb' );

	while ( ! gzeof( $file ) ) {
		fwrite( $out_file, gzread( $file, $buffer_size ) );
	}

	fclose( $out_file );
	gzclose( $file );

	return str_replace( $uploads['basedir'], $uploads['baseurl'], $out_file_name );
}

//https://github.com/nsukonny/unpacker/blob/master/45658_3732636_mp.txt.gz?raw=true
echo custom_file_download_gz( 'https://raw.githubusercontent.com/nsukonny/unpacker/master/45658_3732636_mp.txt.gz' );