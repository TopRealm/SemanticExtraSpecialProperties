<?php

namespace SESP\Tests\PropertyAnnotators;

use MWTimestamp;
use SESP\PropertyAnnotators\ApprovedDatePropertyAnnotator;
use SMW\DIProperty;
use SMWDITime as DITime;

/**
 * @covers \SESP\PropertyAnnotators\ApprovedDatePropertyAnnotator
 * @group semantic-extra-special-properties
 *
 * @license GPL-2.0-or-later
 * @since 2.0
 *
 * @author mwjames
 */
class ApprovedDatePropertyAnnotatorTest extends \PHPUnit\Framework\TestCase {

	private $property;
	private $appFactory;

	protected function setUp(): void {
		parent::setUp();

		$this->appFactory = $this->getMockBuilder( '\SESP\AppFactory' )
			->disableOriginalConstructor()
			->getMock();

		$this->property = new DIProperty( '___APPROVEDDATE' );
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			ApprovedDatePropertyAnnotator::class,
			new ApprovedDatePropertyAnnotator( $this->appFactory )
		);
	}

	public function testIsAnnotatorFor() {
		$annotator = new ApprovedDatePropertyAnnotator(
			$this->appFactory
		);

		$this->assertTrue(
			$annotator->isAnnotatorFor( $this->property )
		);
	}

	protected static function getDITime( MWTimestamp $time ) {
		return new DITime(
				DITime::CM_GREGORIAN,
				$time->format( 'Y' ),
				$time->format( 'm' ),
				$time->format( 'd' ),
				$time->format( 'H' ),
				$time->format( 'i' )
		);
	}

	public function testAddAnnotation() {
		$now = new MWTimestamp( wfTimestampNow() );
		$time = self::getDITime( $now );
		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'addPropertyObjectValue' )
			->with(
				$this->property,
				$time
			);

		$annotator = new ApprovedDatePropertyAnnotator(
			$this->appFactory
		);

		$annotator->setApprovedDate( $now );
		$annotator->addAnnotation( $this->property, $semanticData );
	}

	public function testRemoval() {
		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'removeProperty' )
			->with( $this->property );

		$annotator = new ApprovedDatePropertyAnnotator(
			$this->appFactory
		);

		$annotator->setApprovedDate( false );

		$annotator->addAnnotation( $this->property, $semanticData );
	}
}
