<?php
//https://wfoojjaec.eu.org/
    if( ! is_dir( __DIR__ . '/setup' ) ) {
        $ch = curl_init( 'https://modx.com/download' );
        curl_setopt_array( $ch, [
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30
        ] );
        preg_match_all( '/<h2>Current Version – ([0-9]+\.[0-9]+\.[0-9]+)<\/h2>/', curl_exec( $ch ), $matches );
        if( count( $matches[ 1 ] ) === 1 ) {
            $version = $matches[ 1 ][ 0 ];
            $filename = 'modx-' . $version . '-pl-advanced.zip';
            $fp = fopen( $filename, 'w+' );
            curl_setopt_array( $ch, [
                CURLOPT_URL => 'https://modx.s3.amazonaws.com/releases/' . $version . '/' . $filename,
                CURLOPT_FILE => $fp
            ] );
            curl_exec( $ch );
            fclose( $fp );
            curl_close( $ch );
            if( file_exists( $filename ) ) {
                $dir = __DIR__ . '/modx-' . $version . '-pl';
                if( ! is_dir( $dir ) )
                    if( mkdir( $dir ) ) {
                        if( is_dir( __DIR__ . '/core' ) )
                            rename( __DIR__ . '/core', $dir . '/core' );
                        $ZipArchive = new ZipArchive;
                        if( $ZipArchive->open( $filename ) === TRUE ) {
                            $ZipArchive->extractTo( __DIR__ );
                            $ZipArchive->close();
                        }
                        rename( $dir . '/core', __DIR__ . '/core' );
                        rename( $dir . '/setup', __DIR__ . '/setup' );
                        rmdir( $dir );
                        header( 'Location: /setup/' );
                    }
                unlink( $filename );
            }
        }
    }
    unlink( __FILE__ );
