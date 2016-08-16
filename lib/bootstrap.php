<?php

define( 'ACTION_CMS_ROOT_PATH', realpath( __DIR__ . '/..' ) );
define( 'ACTION_CMS_WP_CORE_PATH', ACTION_CMS_ROOT_PATH . '/opt/automattic/wordpress/src' );

require_once ACTION_CMS_ROOT_PATH . '/opt/autoload.php';

ini_set( 'xdebug.max_nesting_level', 2000 );

class WpPhpParser {
	protected static $instance;
	protected        $fileList;
	protected        $isFinished;
	protected        $parser;

	/**
	 * @return static
	 */
	public static function getInstance() {
		if ( static::$instance ) {
			return static::$instance;
		}

		static::$instance = new static();

		return static::$instance;
	}

	public static function setInstance( $instance ) {
		static::$instance = $instance;
	}

	/**
	 * @return PhpParser\Node[]
	 */
	public function getFileList() {
		if ( $this->fileList && $this->isFinished ) {
			foreach ( $this->fileList as $fileName => $item ) {
				yield $fileName => $item;
			}
		} else {
			foreach ( $this->refresh() as $fileName => $item ) {
				yield $fileName => $item;
			}
		}
	}

	public function refresh() {
		$this->fileList = [ ];

		print "Searching files";

		$find = new \Symfony\Component\Finder\Finder();
		$find->files()->in( ACTION_CMS_WP_CORE_PATH )->name( '*.php' );

		$done   = 0;
		$amount = count( $find );

		printf( "\n%d of %d parsed (%d%%).", $done, $amount, $done * 100 / $amount );

		foreach ( $find as $singleFile ) {
			/** @var \Symfony\Component\Finder\SplFileInfo $singleFile */
			if ( isset( $this->fileList[ $singleFile->getRelativePathname() ] ) ) {
				// seems finished before => reuse
				yield $singleFile->getRelativePathname() => $this->fileList[ $singleFile->getRelativePathname() ];
				continue;
			}

			$nodes = $this->getParser()->parse( $singleFile->getContents() );

			$this->fileList[ $singleFile->getRelativePathname() ] = $nodes;

			printf( "\r%d of %d files parsed (%d%%)", ++ $done, $amount, $done * 100 / $amount );

			yield $singleFile->getRelativePathname() => $nodes;
		}

		$this->isFinished = true;

		print "\n";
	}

	public function setFile( $name, $nodes ) {
		$this->fileList[ $name ] = $nodes;
	}

	/**
	 * @return \PhpParser\Parser
	 */
	protected function getParser() {
		if ( $this->parser ) {
			return $this->parser;
		}

		return $this->parser = ( new \PhpParser\ParserFactory() )->create( \PhpParser\ParserFactory::PREFER_PHP7 );
	}
}