<?php

/**
 * Array_Disk Class
 *
 * Store your array in temporary file to decrease memory usage when having a very very big array.
 *
 * @package   Array Disk
 * @category  File manipulation
 * @author    Muhammad Sofyan <octa7th@gmail.com>
 * @copyright 2014 Muhammad Sofyan
 * @license   http://opensource.org/licenses/MIT
 * @version   0.5.1
 */

class Array_Disk {

	/**
	 * SplFileObject uses for write text into array disk file
	 *
	 * @var SplFileObject
	 */
	private $_write_handle;

	/**
	 * SplFileObject uses for read the array disk file
	 *
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
	 * Magic method construct.
	 * Create array disk file, if $filename parameter is specified then use that file as array disk file
	 * @param string $filename : file name (full path) to be use as array_disk storage
	 * @since 0.1.0
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
	 * @since 0.1.0
	 */
	public function get_filename()
	{
		return $this->_filename;
	}

	/**
	 * Set save flag value
	 * @param boolean $keep Should we keep the file?
	 * @since 0.4.0
	 */
	public function save($keep = TRUE)
	{
		$this->_save = $keep;
	}

	/**
	 * Store a whole array to Array_Disk object.
	 * Use with caution, this method will override existing data
	 * @param array $data
	 * @since 0.1.0
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
	 * @since 0.1.0
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
	 * @since 0.2.0
	 */
	public function push($data = NULL)
	{
		$this->_write_handle->fseek($this->_write_handle->ftell());
		$this->_write_handle->fwrite(json_encode($data) . PHP_EOL);
		$this->_total++;
	}

	/**
	 * Merge array_disk with another array
	 * @since 0.3.0
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
	 * @return mixed The last value of the array
	 * @since 0.2.0
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
	 * @since 0.1.0
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
	 * @since 0.1.0
	 */
	public function read()
	{
		return $this->get($this->_key++);
	}

	/**
	 * Fetch all array data
	 * @return array : a whole array data
	 * @since 0.5.0
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
	 * @since 0.3.0
	 */
	public function rewind()
	{
		$this->_key = 0;
	}

	/**
	 * Get total element / array length
	 * @return int
	 * @since 0.1.0
	 */
	public function length()
	{
		return $this->_total;
	}

	/**
	 * Get total lines of particular file
	 * @param string $filename
	 * @return int Total line number
	 * @since 0.1.1
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

	/**
	 * Magic method destruct.
	 * Reset file handle and remove array disk file if $_save is FALSE
	 * @since 0.1.0
	 */
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