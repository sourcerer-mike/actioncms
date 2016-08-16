<?php


class NoneLeftOverTest extends PHPUnit_Framework_TestCase {

	public function getFiles() {
		foreach ( WpPhpParser::getInstance()->getFileList() as $item ) {
			yield [$item];
		}
	}

	/**
	 * @dataProvider getFiles
	 *
	 * @param \PhpParser\Node $stmt
	 */
	public function testNoDeprecatedFunctionLeftOver( $stmt ) {
		$this->assertNoDeprecation( $stmt );
	}

	/**
	 * @param \PhpParser\Node|string $stmt
	 */
	public function assertNoDeprecation( $stmt ) {
		foreach ( $stmt as $name => $item ) {
			if ( false == $item instanceof \PhpParser\Node\Stmt\Function_ ) {
				continue;
			}

			/** @var PhpParser\Node\Stmt\Function_ $item */

			if ( ! $item->getDocComment() ) {
				continue;
			}

			$this->assertNotContains(
				'@deprecated',
				$item->getDocComment()->getText(),
				$item->name . ' is deprecated.',
				true
			);
		}
	}
}
