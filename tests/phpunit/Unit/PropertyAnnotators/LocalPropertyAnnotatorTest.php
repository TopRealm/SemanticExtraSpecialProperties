<?php

namespace SESP\Tests\PropertyAnnotators;

use SESP\PropertyAnnotators\LocalPropertyAnnotator;
use SMW\DIProperty;
use SMW\DIWikiPage;

/**
 * @covers \SESP\PropertyAnnotators\LocalPropertyAnnotator
 * @group semantic-extra-special-properties
 *
 * @license GPL-2.0-or-later
 * @since 2.0
 *
 * @author mwjames
 */
class LocalPropertyAnnotatorTest extends \PHPUnit\Framework\TestCase {

	private $appFactory;
	private $property;

	protected function setUp(): void {
		parent::setUp();

		$this->appFactory = $this->getMockBuilder( '\SESP\AppFactory' )
			->disableOriginalConstructor()
			->onlyMethods( [ 'getOption' ] )
			->getMock();

		$this->property = new DIProperty( 'FAKE_PROP' );
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			LocalPropertyAnnotator::class,
			new LocalPropertyAnnotator( $this->appFactory )
		);
	}

	public function testIsAnnotatorFor() {
		$instance = new LocalPropertyAnnotator(
			$this->appFactory
		);

		$this->assertTrue(
			$instance->isAnnotatorFor( $this->property )
		);
	}

	public function testAddAnnotation() {
		$subject = DIWikiPage::newFromText( __METHOD__ );

		$callback = static function ( $appFactory, $property, $semanticData ) {
			return $semanticData->getSubject();
		};

		$localPropertyDefinitions = [];

		$localPropertyDefinitions['FAKE_PROP'] = [
			'id'    => 'FAKE_PROP',
			'callback' => $callback
		];

		$this->appFactory->expects( $this->once() )
			->method( 'getOption' )
			->with( 'sespgLocalDefinitions' )
			->willReturn( $localPropertyDefinitions );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'getSubject' )
			->willReturn( $subject );

		$semanticData->expects( $this->once() )
			->method( 'addPropertyObjectValue' );

		$instance = new LocalPropertyAnnotator(
			$this->appFactory
		);

		$instance->addAnnotation( $this->property, $semanticData );
	}

	public function testAddAnnotationOnInvalidLocalDef() {
		$subject = DIWikiPage::newFromText( __METHOD__ );

		$localPropertyDefinitions = [];
		$localPropertyDefinitions['FAKE_PROP'] = [];

		$this->appFactory->expects( $this->once() )
			->method( 'getOption' )
			->with( 'sespgLocalDefinitions' )
			->willReturn( $localPropertyDefinitions );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->never() )
			->method( 'addPropertyObjectValue' );

		$instance = new LocalPropertyAnnotator(
			$this->appFactory
		);

		$instance->addAnnotation( $this->property, $semanticData );
	}

}
