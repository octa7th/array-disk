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

	public function testGetTotalLines()
	{
		$data = array(
			'Test append file 1',
			'Test append file 2',
			'Test append file 3'
		);
		$this->ard->store($data);
		$filename = $this->ard->get_filename();

		// Create new array disk object
		$ard = new Array_Disk($filename);
		$this->assertEquals(3, $ard->length());
	}

	public function testReadArray()
	{
		$data = array(
			'Test append file 1',
			'Test append file 2',
			'Test append file 3'
		);
		$this->ard->store($data);
		$dataStored = array();

		while($d = $this->ard->read())
		{
			$dataStored[] = $d;
		}

		$this->assertEquals($data, $dataStored);
	}

	public function testArrayPop()
	{
		$data = array(
			'Test append file 1',
			'Test append file 2',
			'Test append file 3'
		);
		$this->ard->store($data);
		$popData = $this->ard->pop();
		$this->assertEquals(array_pop($data), $popData);

		$dataStored = array();
		while($d = $this->ard->read())
		{
			$dataStored[] = $d;
		}

		$this->assertEquals($data, $dataStored);
	}

	public function testRewind()
	{
		$data = array(
			'Test append file 1',
			'Test append file 2',
			'Test append file 3'
		);
		$this->ard->store($data);
		$this->ard->read();
		$this->ard->read();
		$this->ard->rewind();
		$this->assertEquals($data[0], $this->ard->read());
		$this->assertEquals($data[1], $this->ard->read());
		$this->assertEquals($data[2], $this->ard->read());
	}

	public function testMerge()
	{
		$data1 = array(
			'Test append file 1',
			'Test append file 2',
			'Test append file 3'
		);
		$data2 = array(
			'Test append file 4',
			'Test append file 5',
			'Test append file 6'
		);
		$this->ard->store($data1);
		$this->ard->merge($data2);

		$dataStored = array();
		while($d = $this->ard->read())
		{
			$dataStored[] = $d;
		}

		$this->assertEquals(array_merge($data1, $data2), $dataStored);
		$this->assertEquals(6, $this->ard->length());

		$this->ard->merge($data2, $data2, $data2);
		$this->ard->rewind();
		$dataStored = array();
		while($d = $this->ard->read())
		{
			$dataStored[] = $d;
		}
		$this->assertEquals(array_merge($data1, $data2, $data2, $data2, $data2), $dataStored);
		$this->assertEquals(15, $this->ard->length());
	}

	public function testSaveToDisk()
	{
		$ard = new Array_Disk();
		$ard->save();
		$filename = $ard->get_filename();
		$ard = NULL;
		$this->assertFileExists($filename);
		unlink($filename);

		$ard = new Array_Disk();
		$ard->save(FALSE);
		$filename = $ard->get_filename();
		$ard = NULL;
		$this->assertFileNotExists($filename);
	}

	public function testFetchAll()
	{
		$data = array(
			'Test append file 1',
			'Test append file 2',
			'Test append file 3'
		);
		$this->ard->store($data);
		$this->assertEquals($data, $this->ard->fetch_all());
	}

}
 