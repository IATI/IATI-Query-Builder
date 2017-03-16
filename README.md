# IATI-Query-Builder

[![License: MIT](https://img.shields.io/badge/license-GPLv3-blue.svg)](https://github.com/IATI/IATI-Query-Builder#licence)

## About

A simple form that will build a query string that can then be used to fetch data from the IATI Datastore API.

See it in action here - http://datastore.iatistandard.org/query/index.php


## Requirements

A webserver running php and php-curl.  PHP dependencies are managed using [Composer](http://culttt.com/2013/01/07/what-is-php-composer/).


## Installation

```
# Clone the repository and enter into the root folder
git clone https://github.com/IATI/IATI-Query-Builder.git
cd IATI-Query-Builder

# Install dependencies
composer install

# If running for the first time, prepare the helper script that enables you to get data for current IATI publishers
# This involves copying the file to a filename of your choice and uncommenting the code
cp helpers/refresh_group_data.example.php helpers/YOUR_FILENAME.php
nano helpers/YOUR_FILENAME.php

# Run the script to get data for current IATI publishers using the CKAN API
php helpers/YOUR_FILENAME.php

# You may wish to ensure that the included codelists are up-to-date (optional)
./update-codelists.sh

# Run a webserver
php -S localhost:8000

# Open a browser and visit localhost:8000
```


## Helper scripts

### Getting the latest IATI publishers
The `/helpers` directory has a script `refresh_group_data.example.php` that will enable you to get data relating to current IATI publishers.

Running this script will generate a `.json` file of 'groups' (i.e. publishers) in use on the IATI registry, and this in turn will populate the 'Reporting Organisation' multi-select element in the form.

### Getting the latest IATI publishers
The script `update-codelists.sh` will re-download latest versions of the required codelists.

### Automating updates
To automate regular updates, you could set-up both of these scripts as a cron job on your server.


## Licence

This file is part of IATI Query Builder.

IATI Query Builder is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

IATI Query Builder is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with IATI Query Builder.  If not, see <http://www.gnu.org/licenses/>.

IATI Query builder relies on other free software products:

### Ckan_client-PHP

Copyright (c) 2010 Jeffrey Barke http://jeffreybarke.net/ https://github.com/jeffreybarke/Ckan_client-PHP Licensed under the MIT license Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

### PHP Markdown

Ckan_client.php includes a copy of Michel Fortin's PHP Markdown (copyright (c) 2004-2009 Michel Fortin http://michelf.com/. All rights reserved.) which is based on on Markdown (copyright (c) 2003-2006 John Gruber http://daringfireball.net/. All rights reserved.).

Read Me: ./lib/php_markdown/PHP Markdown Readme.text License: ./lib/php_markdown/License.text
