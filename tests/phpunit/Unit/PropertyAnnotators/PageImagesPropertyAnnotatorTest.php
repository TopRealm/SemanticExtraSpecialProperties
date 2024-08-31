<?php

namespace SESP\Tests\PropertyAnnotators;

use SESP\AppFactory;
use SESP\PropertyAnnotators\PageImagesPropertyAnnotator;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\SemanticData;
use Title;

/**
 * @covers \SESP\PropertyAnnotators\PageImagesPropertyAnnotator
 * @group semantic-extra-special-properties
 *
 * @license GPL-2.0-or-later
 * @since 2.0
 *
 * @author mwjames
 */
class PageImagesPropertyAnnotatorTest extends \PHPUnit\Framework\TestCase {

	private $property;
	private $appFactory;

	protected function setUp(): void {
		parent::setUp();

		$this->appFactory = $this->getMockBuilder( AppFactory::class )
			->disableOriginalConstructor()
			->getMock();

		$this->property = new DIProperty( '___PAGEIMG' );
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			PageImagesPropertyAnnotator::class,
			new PageImagesPropertyAnnotator( $this->appFactory )
		);
	}

	public function testIsAnnotatorFor() {
		$instance = new PageImagesPropertyAnnotator(
			$this->appFactory
		);

		$this->assertTrue(
			$instance->isAnnotatorFor( $this->property )
		);
	}

	public function testAddAnnotation() {
		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$subject = $this->getMockBuilder( DIWikiPage::class )
			->disableOriginalConstructor()
			->getMock();

		$subject->expects( $this->once() )
			->method( 'getTitle' )
			->willReturn( $title );

		$semanticData = $this->getMockBuilder( SemanticData::class )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'getSubject' )
			->willReturn( $subject );

		$instance = $this->getMockBuilder( PageImagesPropertyAnnotator::class )
			->disableOriginalConstructor()
			->onlyMethods( [ 'getPageImageTitle' ] )
			->getMock();

		$instance->expects( $this->once() )
			->method( 'getPageImageTitle' )
			->willReturn( DIWikiPage::newFromText( __METHOD__ )->getTitle() );

		$instance->addAnnotation( $this->property, $semanticData );
	}

}
