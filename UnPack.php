<?php
/**
 * Class UnPack
 * Upload gz file for server without packing
 */

class UnPack {

	/**
	 * Prepare file by URL
	 *
	 * @param $url
	 */
	public function get_file( $url ) {

		$file = $this->download( $url );
		$this->unzip( $file );

	}

	/**
	 * Download file from URL
	 *
	 * @param $url
	 * @param string $type
	 *
	 * @return mixed
	 */
	private function download( $url ) {

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "GET" );

		$result = curl_exec( $ch );
		if ( curl_errno( $ch ) ) {
			exit( 'Error:' . curl_error( $ch ) );
		}
		curl_close( $ch );

		$uploads = array(
			'basedir' => __DIR__,
			'baseurl' => $_SERVER['REQUEST_URI']
		);

		if ( function_exists( 'wp_upload_dir' ) ) {
			$uploads = wp_upload_dir();
		}

		$filename = $uploads['basedir'] . '/' . strtok( basename( $url ), "?" );
		if ( file_exists( $filename ) ) {
			@unlink( $filename );
		}
		file_put_contents( $filename, $result );

		return str_replace( $uploads['basedir'], $uploads['baseurl'], $filename );
	}

	/**
	 * Unpack file from gz
	 *
	 * @param $file
	 */
	private function unzip( $file ) {

		$buffer_size   = 4096;
		$out_file_name = str_replace( '.gz', '.csv', $file );

		$file     = gzopen( $file, 'rb' );
		$out_file = fopen( $out_file_name, 'wb' );

		while ( ! gzeof( $file ) ) {
			fwrite( $out_file, gzread( $file, $buffer_size ) );
		}

		fclose( $out_file );
		gzclose( $file );
	}

}

// Run script
$unpack = new UnPack();
$unpack->get_file( 'https://raw.githubusercontent.com/nsukonny/unpacker/master/example.csv.gz' );


function custom_file_download_gz( $url ) {

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "GET" );

	$result = curl_exec( $ch );
	if ( curl_errno( $ch ) ) {
		exit( 'Error:' . curl_error( $ch ) );
	}
	curl_close( $ch );

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
	$out_file_name = str_replace( '.gz', '.csv', $filename );

	$file     = gzopen( $filename, 'rb' );
	$out_file = fopen( $out_file_name, 'wb' );

	while ( ! gzeof( $file ) ) {
		fwrite( $out_file, gzread( $file, $buffer_size ) );
	}

	fclose( $out_file );
	gzclose( $file );

	return str_replace( $uploads['basedir'], $uploads['baseurl'], $out_file_name );
}

echo custom_file_download_gz( 'https://raw.githubusercontent.com/nsukonny/unpacker/master/example.csv.gz' );