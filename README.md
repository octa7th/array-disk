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
require 'Array_Disk.php'

$ard = new Array_Disk();

$ard->append('Value 0');
$ard->append('Value 1');
$ard->append('Value 2');

$ard->get(1); // return 'Value 1'

// You can also use store method
$data = array(
    'Value 0',
    'Value 1',
    'Value 2'
);
$ard->store($data);
$ard->length(); // return 3
```

## Changelog
* 0.1.0 :
    * Original Class
* 0.1.1 :
    * Create new method to get line number of particular file
    * If filename is defined in construct parameter, then use that file's line number as array length

## License
### The MIT License (MIT)

Copyright (c) 2014, Muhammad Sofyan <octa7th@gmail.com>

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