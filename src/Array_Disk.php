<?php

/**
 * Array_Disk Class
 *
 * @category  File manipulation
 * @author    Muhammad Sofyan <octa7th@gmail.com>
 * @copyright Copyright (c) 2014
 * @license   http://opensource.org/licenses/MIT
 * @version   0.1.2
 */

class Array_Disk {

	/**
	 * @var SplFileObject
	 */
	private $_write_handle;

	/**
	 * @var SplFileObject
	 */
	private $_read_handle;

	/**
	 * @var string tmp folder path
	 */
	private $_tmp;

	/**
	 * @var string temporary file for Array_Disk Object
	 */
	private $_filename;

	/**
	 * @var int array length
	 */
	private $_total;

	/**
	 * @var int current array key
	 */
	private $_key;

	function __construct($filename = '')
	{
		$this->_key = 0;
		$this->_tmp = '/tmp/';

		if($filename === '')
		{
			$unique = uniqid('ard_');
			$this->_total    = 0;
			$this->_filename = $this->_tmp . $unique . '.ard';
		}
		else
		{
			$this->_filename = $filename;
			if(file_exists($filename))
			{
				$this->_total    = $this->get_total_lines($filename);
			}
		}
		$this->_write_handle = new SplFileObject($this->_filename, "w");
		$this->_read_handle  = new SplFileObject($this->_filename, "r");
	}

	/**
	 * Get filename of Array_Disk object storage
	 * @return string
	 */
	public function get_filename()
	{
		return $this->_filename;
	}

	/**
	 * Store a whole array to Array_Disk object
	 * Override data if it's not empty
	 * @param array $data
	 */
	public function store(array $data)
	{
		$this->_write_handle->ftruncate(0);
		$this->_write_handle->fseek(0);
		foreach($data as $d)
		{
			$this->_write_handle->fwrite(json_encode($d) ."\n");
		}
		$this->_total = count($data);
	}

	/**
	 * Append new element to Array_Disk Object
	 * @param mixed $data
	 */
	public function append($data = NULL)
	{
		$this->_write_handle->fseek($this->_write_handle->ftell());
		$this->_write_handle->fwrite(json_encode($data) ."\n");
		$this->_total++;
	}

	/**
	 * Get value of certain key
	 * @param int $key
	 * @return mixed the element's value
	 */
	public function get($key = 0)
	{
		$this->_read_handle->seek($key);
		$data = $this->_read_handle->current();
		return json_decode($data, TRUE);
	}

	/**
	 * Read all array data
	 * Use this for iteration
	 * @return mixed the element's value
	 */
	public function read()
	{
		return $this->get($this->_key++);
	}

	/**
	 * Get total array
	 * @return int
	 */
	public function length()
	{
		return $this->_total;
	}

	/**
	 * Get total lines of particular file
	 * @param string $filename
	 * @return int Total line number
	 */
	public function get_total_lines($filename = '')
	{
		$lineCount = 0;

		if(file_exists($filename))
		{
			$handle = fopen($filename, "r");
			while( ! feof($handle) )
			{
				$line      = fgets($handle, 4096);
				$lineCount = $lineCount + substr_count($line, PHP_EOL);
			}
			fclose($handle);
		}

		return $lineCount;
	}

	function __destruct()
	{
		if(file_exists($this->_filename))
		{
			unset($this->_write_handle);
			unset($this->_read_handle);
			unlink($this->_filename);
		}
	}

} 