<?php

spl_autoload_register(function ($className) {
    # Usually I would just concatenate directly to $file variable below
    # this is just for easy viewing on Stack Overflow)
        $ds = DIRECTORY_SEPARATOR;
        $dir = __DIR__;

    // replace namespace separator with directory separator (prolly not required)
        $className = str_replace('\\', $ds, $className);
        #print "className :: $className <br>";

    // get full name of file containing the required class
        $file = "{$dir}/lib/{$ds}{$className}.php";
        #print "file lib: $file <br>";

    // get file if it is readable
        if (file_exists($file)) {
          #print "lib :: $file <br>";
          require_once $file;
        }  else #print " NO ";

        $file = "{$dir}/src/{$ds}{$className}.php";
        #print "file src: $file <br>";

        if (file_exists($file)) {
          #print "src :: $file <br>";
          require_once $file;
        }

});


function autoload ($directory) {
		// Get a listing of the current directory
		$scanned_dir = scandir( $directory );
		if ( empty( $scanned_dir ) ) {
			return;
		}
		// Ignore these items from scandir
		$ignore = array( '.', '..' );
		// Remove the ignored items
		$scanned_dir = array_diff( $scanned_dir, $ignore );
		foreach ( $scanned_dir as $item ) {
			$filename = $directory . '/' . $item;
			$real_path = realpath( $filename );
			if ( false === $real_path ) {
				continue;
			}
			$filetype = filetype( $real_path );
			if ( empty( $filetype ) ) {
				continue;
			}
			// If it's a directory then recursively load it
			if ( 'dir' === $filetype ) {
				autoload( $real_path );
			}
			// If it's a file, let's try to load it
			else if ( 'file' === $filetype ) {
				// Don't allow files that have been uploaded
				if ( is_uploaded_file( $real_path ) ) {
					continue;
				}
				// Don't load any files that are not the proper mime type
				if ( 'text/x-php' !== mime_content_type( $real_path ) ) {
					continue;
				}
				$filesize = filesize( $real_path );
				// Don't include empty or negative sized files
				if ( $filesize <= 0 ) {
					continue;
				}
				// Don't include files that are greater than 100kb
				if ( $filesize > 100000 ) {
					continue;
				}
				$pathinfo = pathinfo( $real_path );
				// An empty filename wouldn't be a good idea
				if ( empty( $pathinfo['filename'] ) ) {
					continue;
				}
				// Sorry, need an extension
				if ( empty( $pathinfo['extension'] ) ) {
					continue;
				}
				
				// Actually, we want just a PHP extension!
				if ( 'php' !== $pathinfo['extension'] ) {
					continue;
				}
				
				// Only for files that really exist
				if ( true !== file_exists( $real_path ) ) {
					continue;
				}
				if ( true !== is_readable( $real_path ) ) {
					continue;
				}
        #print $real_path . "<br>";
				require_once( $real_path );
			}
		}
}

