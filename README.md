# Crawling System

## Description
A simple app to crawl all products of Lock&Lock brand and save to database.
This is the demo link: https://lovetech.biz/products?page=1

## Features
- Crawl all products of the Lock&Lock brand.
- Have a basic website to display products with pagination.

## Getting Started
Follow these steps to set up and use the application:

### Prerequisites
- Docker: Ensure Docker is installed on your system. You can download and install Docker from [here](https://www.docker.com/get-started).
- Docker Compose: Make sure Docker Compose is installed on your system. You can download and install Docker Compose from [here](https://docs.docker.com/compose/install/).

### Installation
1. Clone the repository: `git clone https://github.com/hieunguyenm96/crawling-system.git`
2. Navigate to the project directory: `cd project-directory`

### Configuration
1. Open the `.env` file.
2. Configure the database as needed.

### Usage
1. Run the application: `docker compose up -d --wait`
2. Follow the prompts or command-line options to initiate the crawling process.
3. Monitor the console output or log files for progress and results.

### Example
An example command for running the application with specific parameters or options:

```bash
# Prepare the csv file 
php bin/console app:fresh-product-csv
# Crawl data from the target site and write to file
php bin/console app:crawl-products --crawlAll=true --cookie="GET_IN_YOUR_BROWSER_WHEN_YOU_ACCESS_THE_TARGET_SITE"
# Read file and save to database
php bin/console app:import-product
