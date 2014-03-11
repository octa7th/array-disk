<?php

/**
 * Array_Disk Class PHPUnit Test
 *
 * @category  PHPUnit Test Class
 * @author    Muhammad Sofyan <octa7th@gmail.com>
 * @copyright Copyright (c) 2014
 * @license   http://opensource.org/licenses/MIT
 */

class Array_DiskTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Array_Disk
	 */
	public $ard;

	public function __construct()
	{
		parent::__construct();
		$this->ard = new Array_Disk();
	}

	public function testConstruct()
	{
		$this->assertTrue($this->ard instanceof Array_Disk);
	}

	public function testAppend()
	{
		$text1 = 'Test append file 1';
		$text2 = 'Test append file 2';
		$text3 = 'Test append file 3';
		$this->ard->append($text1);
		$this->ard->append($text2);
		$this->ard->append($text3);
		$this->assertEquals(3, $this->ard->length());
		$this->assertEquals($text3, $this->ard->get(2));
		$this->assertEquals('Test append file 1', $this->ard->read());
		$this->assertEquals('Test append file 2', $this->ard->read());
	}

	public function testStoreAndGetElement()
	{
		$data = array(
			'Test append file 1',
			'Test append file 2',
			'Test append file 3'
		);
		$this->ard->store($data);
		$this->assertEquals(3, $this->ard->length());
		$this->assertEquals($data[0], $this->ard->get(0));
		$this->assertEquals($data[2], $this->ard->get(2));
	}
}
 