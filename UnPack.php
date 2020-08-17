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

		//$uploads  = wp_upload_dir();
		$filename = strtok( basename( $url ), "?" );
		if ( file_exists( $filename ) ) {
			@unlink( $filename );
		}
		file_put_contents( $filename, $result );

		return $filename;
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

echo 'asdf';

// Run script
$unpack = new UnPack();
$unpack->get_file( 'https://files.pythonhosted.org/packages/79/0c/d0eaa9380189a292121acab65199ac95b9209b45006ad8aa5266abd36943/backports.csv-1.0.7.tar.gz' );