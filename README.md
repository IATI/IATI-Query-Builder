# IATI-Query-Builder

[![License: MIT](https://img.shields.io/badge/license-GPLv3-blue.svg)](https://github.com/IATI/IATI-Query-Builder#licence)

## About

A simple form that will build a query string that can then be used to fetch data from the IATI Datastore API.

See it in action here - http://datastore.iatistandard.org/query


## Requirements

<!-- A webserver with apache? -->
TBC


## Installation

```
# Clone the repository and enter into the root folder
git clone https://github.com/IATI/IATI-Query-Builder.git
cd IATI-Query-Builder

Activate Python virtual environment.

# Install dependencies
pip install -r requirements.txt

# Run the script to get data for current IATI publishers using the CKAN API
# Ideally set this up as a regular cron job
<!-- Add instructions for get_publishers.py -->

# Run the flask app
flask run

# Open a browser and visit localhost:5000
```
