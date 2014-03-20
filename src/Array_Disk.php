<?php

/**
 * Array_Disk Class
 *
 * @category  File manipulation
 * @author    Muhammad Sofyan <octa7th@gmail.com>
 * @copyright Copyright (c) 2014
 * @license   http://opensource.org/licenses/MIT
 * @version   0.5.1
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
	 * @var string temporary folder path
	 */
	private $_temp;

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

	/**
	 * @var bool : save flag
	 */
	private $_save;

	/**
	 * @param string $filename : file name (full path) to be use as array_disk storage
	 */
	function __construct($filename = '')
	{
		$this->_key   = 0;
		$this->_temp  = '/tmp/';
		$this->_save  = FALSE;
		$this->_total = 0;

		if($filename === '')
		{
			$unique = uniqid('ard_');
			$this->_filename = $this->_temp . $unique . '.ard';
		}
		else
		{
			$this->_filename = $filename;
			if(file_exists($filename))
			{
				$this->_total    = $this->get_total_lines($filename);
			}
		}
		$this->_write_handle = new SplFileObject($this->_filename, 'w');
		$this->_read_handle  = new SplFileObject($this->_filename, 'r');
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
	 * Set save flag value
	 * @param boolean : save flag
	 */
	public function save($keep = TRUE)
	{
		$this->_save = $keep;
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
		foreach($data as $element)
		{
			$this->_write_handle->fwrite(json_encode($element) . PHP_EOL);
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
		$this->_write_handle->fwrite(json_encode($data) . PHP_EOL);
		$this->_total++;
	}

	/**
	 * Push new element to Array_Disk Object (alias of append)
	 * @param mixed $data
	 */
	public function push($data = NULL)
	{
		$this->_write_handle->fseek($this->_write_handle->ftell());
		$this->_write_handle->fwrite(json_encode($data) . PHP_EOL);
		$this->_total++;
	}

	/**
	 * Merge array_disk with another array
	 */
	public function merge()
	{
		$args = func_get_args();
		$this->_write_handle->fseek($this->_write_handle->ftell());

		foreach($args as $data)
		{
			if(is_array($data))
			{
				foreach($data as $element)
				{
					$this->_write_handle->fwrite(json_encode($element) . PHP_EOL);
				}
			}
			$this->_total += count($data);
		}
	}

	/**
	 * Pop the element off the end of array
	 *
	 * @return mixed the last value of the array
	 */
	public function pop()
	{
		$last_line = $this->_total - 1;
		$this->_read_handle->seek($last_line);
		$json_data = $this->_read_handle->current();
		$length   = strlen($json_data);
		$truncate = $this->_write_handle->ftell() - $length;
		$this->_write_handle->ftruncate($truncate);
		$this->_write_handle->fseek($truncate);
		$this->_total--;
		return json_decode($json_data, TRUE);
	}

	/**
	 * Get value of certain key
	 * @param int $array_key
	 * @return mixed the element's value
	 */
	public function get($array_key = 0)
	{
		$this->_read_handle->seek($array_key);
		$data = $this->_read_handle->current();
		return json_decode($data, TRUE);
	}

	/**
	 * Read array data in current line
	 * Use this for iteration
	 * @return mixed the element's value
	 */
	public function read()
	{
		return $this->get($this->_key++);
	}

	/**
	 * Fetch all array data
	 * @return array : a whole array data
	 */
	public function fetch_all()
	{
		$this->rewind();
		$data = array();

		while($element = $this->read())
		{
			$data[] = $element;
		}

		$this->rewind();
		return $data;
	}

	/**
	 * Rewind the file to the first line
	 */
	public function rewind()
	{
		$this->_key = 0;
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
		$line_count = 0;

		if(file_exists($filename))
		{
			$handle = fopen($filename, 'r');
			while( ! feof($handle) )
			{
				$line      = fgets($handle, 4096);
				$line_count = $line_count + substr_count($line, PHP_EOL);
			}
			fclose($handle);
		}

		return $line_count;
	}

	function __destruct()
	{
		if(file_exists($this->_filename))
		{
			unset($this->_write_handle);
			unset($this->_read_handle);
			if( ! $this->_save ) unlink($this->_filename);
		}
	}

} 