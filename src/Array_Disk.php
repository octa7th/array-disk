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
 * @version   0.9.0
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
	 * Key of array to sort
	 * @var string
	 */
	private $_sort_key = "";

	private $_method = 'json';

	/**
	 * Magic method construct.
	 * Create array disk file, if $filename parameter is specified then use that file as array disk file
	 * @param string $filename : file name (full path) to be use as array_disk storage
	 * @param string $sortKey
	 * @since 0.1.0
	 */
	function __construct($filename = '', $sortKey = "")
	{
		$this->_key   = 0;
		$this->_temp  = '/tmp/';
		$this->_save  = FALSE;
		$this->_total = 0;
		$this->_sort_key = $sortKey;

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
		$this->_write_handle = new SplFileObject($this->_filename, 'a');
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
	 * Select method to store text file (json|serialize)
	 * @param string $method
	 * @since 0.9.0
	 */
	public function set_method($method = 'json')
	{
		$this->_method = $method;
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
	 * @param string $sort
	 * @since 0.1.0
	 */
	public function store(array $data, $sort = "")
	{
		$this->_write_handle->ftruncate(0);
		$this->_write_handle->fseek(0);
		foreach($data as $element)
		{
			$sortVal = "";
			if($this->_sort_key !== "")
			{
				$sortVal = self::get_value($data, $this->_sort_key);
				$sortVal = "$sortVal]|";
			}

			if($this->_method === 'json')
			{
				$this->_write_handle->fwrite($sort . $sortVal . json_encode($element) . PHP_EOL);
			}
			else
			{
				$this->_write_handle->fwrite($sort . $sortVal . serialize($element) . PHP_EOL);
			}
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
		$sortVal = "";
		if($this->_sort_key !== "")
		{
			$keys = explode('.', $this->_sort_key);
			$sortVal = self::get_value($data, $keys);
			$sortVal = "$sortVal]|";
		}
		$this->_write_handle->fseek($this->_write_handle->ftell());
		if($this->_method === 'json')
		{
			$this->_write_handle->fwrite($sortVal . json_encode($data) . PHP_EOL);
		}
		else
		{
			$this->_write_handle->fwrite($sortVal . serialize($data) . PHP_EOL);
		}
		$this->_total++;
	}

	/**
	 * Get value from an array, recursively
	 * @param $data
	 * @param array $keys
	 * @return mixed
	 * @since 0.6.0
	 */
	public static function get_value($data, array $keys)
	{
		if(empty($keys)) return $data;

		$key = array_shift($keys);

		if((array) $data === $data)
		{
			if(isset($data[$key]))
			{
				return self::get_value($data[$key], $keys);
			}
		}
		else if(is_object($data))
		{
			if(isset($data->$key))
			{
				return self::get_value($data->$key, $keys);
			}
		}

		return NULL;
	}

	/**
	 * Push new element to Array_Disk Object (alias of append)
	 * @param mixed $data
	 * @since 0.2.0
	 */
	public function push($data = NULL)
	{
		$this->append($data);
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
					$sortVal = "";
					if($this->_sort_key !== "")
					{
						$sortVal = self::get_value($element, $this->_sort_key);
						$sortVal = "$sortVal]|";
					}
					if($this->_method === 'json')
					{
						$this->_write_handle->fwrite($sortVal . json_encode($element) . PHP_EOL);
					}
					else
					{
						$this->_write_handle->fwrite($sortVal . serialize($element) . PHP_EOL);
					}
				}
			}
			$this->_total += count($data);
		}
	}

	/**
	 * Concat / Merge Array_Disk object with another Array_Disk object
	 * @param Array_Disk $disk
	 * @since 0.8.0
	 */
	public function concat(Array_Disk $disk)
	{
		$disk->rewind();
		while($data = $disk->read())
		{
			$this->push($data);
		}
		$disk->rewind();
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
		return $this->parse_line($json_data);
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
		return $this->parse_line($data);
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
	 * Remove sort value.
	 * Convert string to data using json_decode
	 * @param string $textLine
	 * @return mixed
	 * @since 0.6.0
	 */
	private function parse_line($textLine = "")
	{
		if(preg_match('/^.*?\]\|/', $textLine))
		{
			$line = preg_replace('/^.*?\]\|/', '', $textLine);
		}
		else
		{
			$line = $textLine;
		}
		if($this->_method === 'json')
		{
			return json_decode($line, TRUE);
		}
		else
		{
			return unserialize($line);
		}
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
	 * Slice array
	 * @param int $offset
	 * @param null $count
	 * @return array
	 * @since 0.7.0
	 */
	public function slice($offset = 0, $count = NULL)
	{
		$count = is_null($count) ? $this->_total - $offset : $count;
		$data = array();

		while($offset < $this->_total && count($data) < $count)
		{
			$data[] = $this->get($offset++);
		}
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
	 * Sort Array Disk object
	 * @param string $key Key from array to sort
	 * @param string $option See linux sort option
	 * @return bool
	 * @since 0.6.0
	 */
	public function sort($key = "", $option = "")
	{
		$filename = $this->_filename;
		if($key !== "")
		{
			$unique = uniqid();
			$filename = $this->_filename . $unique;
			$sortedArrayDisk = new Array_Disk($filename, $key);
			$sortedArrayDisk->save();
			$this->rewind();

			while($data = $this->read())
			{
				$sortedArrayDisk->push($data);
			}

			unset($sortedArrayDisk);
		}
		$this->_sort_array_file($filename, $option);
		return $this->_change_array_file($filename);
	}

	/**
	 * Change array disk main file storage
	 * @param string $filename
	 * @return bool
	 * @since 0.6.0
	 */
	private function _change_array_file($filename = "")
	{
		if(!file_exists($filename)) return FALSE;
		$this->rewind();
		unset($this->_write_handle);
		unset($this->_read_handle);
		if($this->_filename !== $filename) unlink($this->_filename);
		$this->_filename = $filename;
		$this->_write_handle = new SplFileObject($this->_filename, 'a');
		$this->_read_handle  = new SplFileObject($this->_filename, 'r');
		return TRUE;
	}

	/**
	 * Sort array using linux script
	 * @param $filename
	 * @param string $option
	 * @return bool
	 * @since 0.6.0
	 */
	private function _sort_array_file($filename, $option = "")
	{
		if( ! file_exists($filename) ) return FALSE;
		if($option !== "")
		{
			shell_exec("sort -$option '$filename' > '$filename.sort'");
		}
		else
		{
			shell_exec("sort '$filename' > '$filename.sort'");
		}
		shell_exec("mv '$filename.sort' '$filename'");

		return TRUE;
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
