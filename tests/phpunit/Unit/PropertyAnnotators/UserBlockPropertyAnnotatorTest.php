<?php

namespace SESP\Tests\PropertyAnnotators;

use MediaWiki\Block\DatabaseBlock;
use SESP\AppFactory;
use SESP\PropertyAnnotators\UserBlockPropertyAnnotator;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\SemanticData;
use Title;
use User;

/**
 * @covers \SESP\PropertyAnnotators\UserBlockPropertyAnnotator
 * @group semantic-extra-special-properties
 *
 * @license GPL-2.0-or-later
 * @since 2.0
 *
 * @author mwjames
 */
class UserBlockPropertyAnnotatorTest extends \PHPUnit\Framework\TestCase {

	private $property;
	private $appFactory;

	protected function setUp(): void {
		parent::setUp();

		$this->appFactory = $this->getMockBuilder( AppFactory::class )
			->disableOriginalConstructor()
			->getMock();

		$this->property = new DIProperty( '___USERBLOCK' );
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			UserBlockPropertyAnnotator::class,
			new UserBlockPropertyAnnotator( $this->appFactory )
		);
	}

	public function testIsAnnotatorFor() {
		$instance = new UserBlockPropertyAnnotator(
			$this->appFactory
		);

		$this->assertTrue(
			$instance->isAnnotatorFor( $this->property )
		);
	}

	/**
	 * @dataProvider blockActionProvider
	 */
	public function testAddAnnotation( $action, $expected ) {
		$compare = static function ( $reason ) use( $action ) {
			return $reason == $action;
		};

		$block = $this->getMockBuilder( DatabaseBlock::class )
			->disableOriginalConstructor()
			->getMock();

		$block->expects( $this->any() )
			->method( 'appliesToRight' )
			->willReturnCallback( $compare );

		$user = $this->getMockBuilder( User::class )
			->disableOriginalConstructor()
			->getMock();

		$user->expects( $this->once() )
			->method( 'getBlock' )
			->willReturn( $block );

		$this->appFactory->expects( $this->once() )
			->method( 'newUserFromTitle' )
			->willReturn( $user );

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'inNamespace' )
			->willReturn( true );

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

		$semanticData->expects( $expected )
			->method( 'addPropertyObjectValue' );

		$instance = new UserBlockPropertyAnnotator(
			$this->appFactory
		);

		$instance->addAnnotation( $this->property, $semanticData );
	}

	public function blockActionProvider() {
		$provider[] = [
			'Foo',
			$this->never()
		];

		$provider[] = [
			'edit',
			$this->once()
		];

		$provider[] = [
			'createaccount',
			$this->once()
		];

		$provider[] = [
			'sendemail',
			$this->once()
		];

		$provider[] = [
			'editownusertalk',
			$this->once()
		];

		$provider[] = [
			'read',
			$this->once()
		];

		return $provider;
	}

}
