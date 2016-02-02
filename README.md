[![Build Status](https://travis-ci.org/octa7th/array-disk.svg?branch=master)](https://travis-ci.org/octa7th/array-disk)

# Array Disk
Store your array in temporary file to decrease memory usage when having a very very big array.

## Requirement
* PHP >= 5.1.0

## Features
* Create array_disk using existing array
* Fetch array_disk line by line
* Fetch value using array_disk's key
* Append new value to array_disk

## How to use
```php
require 'Array_Disk.php';

// Create new array disk object
$ard = new Array_Disk();

$data = array( 'Value 0', 'Value 1', 'Value 2' );

$ard->store($data); // Store a whole array in array disk object

$ard->length(); // Get current array length (return 3)

$ard->append('Value 3'); // Append value to array disk object

$ard->push('Value 4'); // Alias of append

$ard->length(); // return 5

$ard->pop(); // Remove last element from array disk object and return the last element (return 'Value 4')

$ard->get(1); // Get array value in key 1 (return 'Value 1')

$ard->merge(array('Value 5', 'Value 6')); // Merge array disk object with another array

$ard->length(); // return 6

$ard->slice(2, 3); // return array('Value 2', 'Value 3', 'Value 4')

$filename = $ard->get_filename(); // Get filename of array disk object storage

$ard->read(); // Read array value from the first line (return 'Value 0')
$ard->read(); // return 'Value 1'
$ard->read(); // return 'Value 2'
$ard->read(); // return 'Value 3'
$ard->read(); // return 'Value 5'
$ard->read(); // return 'Value 6'

$ard->rewind(); // Reset cursor back to the first line

$ard->sort(); // Sort data

```

## Changelog
* 0.1.0 :
    * Original Class
* 0.1.1 :
    * Create new method to get line number of particular file
    * If filename is defined in construct parameter, then use that file's line number as array length
* 0.1.2 :
    * Fix bugs #1
* 0.2.0 :
    * Create new method push (alias of append)
    * Create new method pop
* 0.3.0 :
    * Create new method rewind
    * Create new method merge
* 0.4.0 :
    * Create new method save
* 0.4.1 :
    * Change \\n to PHP_EOL
* 0.4.2 :
    * Support multiple array in merge method
* 0.5.0 :
    * Create new method fetch_all
* 0.5.1 :
    * Fix bugs total is NULL
* 0.5.2 :
    * Fix bugs data loss if file is not empty
* 0.6.0 :
    * Create new method sort
* 0.7.0 :
    * Create new method slice
* 0.7.1 :
    * Fix preg_replace issue
* 0.8.0 :
    * New method concat
* 0.9.0 :
    * Add new option to use php serialize instead of json
    * Optimize code

## License
### The MIT License (MIT)

Copyright (c) 2014 - 2015, Muhammad Sofyan \<<octa7th@gmail.com>\>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.