# dantour

dantour plugin for wp

## Purpose
A  convert to convert  json or txt data to  wp-travel csv format


### Run the code
php run.php [fileName.(json|txt)]  --format=(json|txt)

###  verrify json data
php verification.php 


### Example of txt
```
##概述
芬兰冰岛8日深度豪华行程

--------------------------
##天数
Day01

##城市
杭州-斯德哥尔摩

##日期
4月27日

##早餐
/

##午餐
/

##晚餐
/

##交通
全天：中国出发，前往瑞典斯德哥尔摩

##住宿
HILTON KALASTAJATORPA（5星级会议酒店）靠近市中心，环境优美，交通方便。

##活动
全员抵达哥本哈根，安排哥本哈根最受欢迎的牛排餐厅晚餐，开团仪式！

--------------------------
```

### Example of json 

```
{
  "tripData": {
    "题目": "芬兰冰岛8日深度豪华行程",

    "概要": "芬兰冰岛8日深度豪华行程"
  },
  "daysData": [
    {
      "天数": "Day01",
      "城市": "杭州-斯德哥尔摩",
      "日期": "4月27日",
      "早餐": "/",
      "午餐": "/",
      "晚餐": "/",
      "交通": "全天：中国出发，前往瑞典斯德哥尔摩",
      "住宿": "HILTON KALASTAJATORPA（5星级会议酒店）靠近市中心，环境优美，交通方便。",
      "活动": [
        "-- 全员抵达哥本哈根，安排哥本哈根最受欢迎的牛排餐厅晚餐，开团仪式！"
      ]
    },
    {
      "天数": "Day02",
      "城市": "斯德哥尔摩",
      "日期": "4月28日",
      "早餐": "/",
      "午餐": "/",
      "晚餐": "欢迎晚宴",
      "交通": "/",
      "住宿": "赫尔辛基",
      "活动": [
        "-- 人文体验一 | 斯德哥尔摩City Walk, 诺贝尔博物馆、斯德哥尔摩市政厅、瓦萨沉船博物馆等瑞典地标导览。",
        "-- 人文体验二 | 沉浸式体验瑞典Fika文化, 沉浸式体验瑞典Fika文化，感受当地的咖啡休闲传统。"
      ]
    }
]}
```


### TLTR

#### CSV Generator from Itinerary Data Files

This project provides a PHP script that processes itinerary data files in JSON or TXT format and generates an output CSV file suitable for importing into systems like WordPress with the WP Travel plugin. The script supports both JSON and TXT formats and allows you to specify the format and file pattern through command-line arguments.

#### Table of Contents

	•	Features
	•	Prerequisites
	•	Installation
	•	Directory Structure
	•	Usage
	•	Basic Command
	•	Specifying the Format
	•	Using a File Pattern
	•	Examples
	•	Configuration
	•	Output
	•	Extending the Script
	•	Error Handling
	•	Troubleshooting
	•	License

####  Features

	•	Supports JSON and TXT Formats: Process itinerary data files in either JSON or TXT format.
	•	Command-Line Interface: Easy to use with command-line arguments for format and file patterns.
	•	Customizable: Extend or modify the conversion functions to suit your data structure.
	•	Generates Import-Ready CSV: Creates a CSV file suitable for importing into systems like WordPress.

#### Prerequisites

	•	PHP: Ensure that PHP (version 7.0 or higher) is installed on your system and accessible from the command line.
	•	Composer (optional): For dependency management if you plan to extend the project.
	•	Data Files: Itinerary data files in JSON or TXT format.
	•	Template CSV: A template.csv file that defines the structure of the output CSV.

#### Installation

	1.	Clone the Repository:
```
git clone https://github.com/yourusername/your-repo-name.git
cd your-repo-name
```

	2.	Prepare the Data Directories:
	•	Create the following directories if they don’t exist:
```
mkdir -p data/json
mkdir -p data/txt
```

	3.	Place Your Data Files:
	•	Put your JSON files into data/json/.
	•	Put your TXT files into data/txt/.
	4.	Ensure the Template CSV Exists:
	•	Place your template.csv file in the root directory of the project.

#### Directory Structure

your-repo-name/
├── data/
│   ├── json/
│   │   └── your-json-files.json
│   └── txt/
│       └── your-txt-files.txt
├── _gen-wp-term.php
├── _pre_json.php
├── _pre_txt.php
├── template.csv
├── your_script.php
└── README.md

	•	data/: Contains subdirectories for JSON and TXT data files.
	•	_gen-wp-term.php: Contains functions for handling taxonomies.
	•	_pre_json.php: Contains the convert_json_to_data() function.
	•	_pre_txt.php: Contains the convert_txt_to_data() function.
	•	template.csv: The template CSV file used as a base for generating the output.
	•	your_script.php: The main script that processes the data files.
	•	README.md: This documentation file.

#### Usage

Navigate to the project directory and run the script using PHP from the command line.

#### Basic Command

php your_script.php

	•	Processes all .json files in the ./data/json directory.
	•	Uses the convert_json_to_data() function.
	•	Generates an output CSV file named output.csv.

#### Specifying the Format

php your_script.php --format=txt

	•	Processes all .txt files in the ./data/txt directory.
	•	Uses the convert_txt_to_data() function.
	•	Generates an output CSV file named output.csv.

#### Using a File Pattern

php your_script.php --format=json 'trip_*.json'

	•	Processes files matching trip_*.json in the ./data/json directory.
	•	Generates an output CSV file named output_trip_*.json.csv.

#### Examples

	•	Process all JSON files:
```
php your_script.php
```

	•	Process all TXT files:
```
php your_script.php --format=txt
```

	•	Process specific JSON files:
```
php your_script.php --format=json 'trip_2023_*.json'
```

	•	Process specific TXT files:
```
php your_script.php --format=txt 'itinerary_*.txt'
```


#### Configuration

	•	Template CSV (template.csv): Ensure this file contains the required columns:
	•	wp_travel_trip_itinerary_data
	•	post_title
	•	post_excerpt
	•	taxonomies
	•	Conversion Functions:
	•	JSON: Implement convert_json_to_data($filePath) in _pre_json.php.
	•	TXT: Implement convert_txt_to_data($filePath) in _pre_txt.php.
	•	Taxonomies: The script uses a function getTaxonomies() from _gen-wp-term.php. Ensure this function is properly defined.

#### Output

	•	Output CSV File: The script generates an output CSV file named output.csv by default or output_<file_pattern>.csv if a file pattern is specified.
	•	Content: The CSV file contains the processed itinerary data, including serialized itineraries, post titles, excerpts, and taxonomies.

#### Extending the Script

Adding Support for New Formats

	1.	Create a New Conversion Function:
	•	For a new format (e.g., XML), create a file _pre_xml.php.
	•	Define a function convert_xml_to_data($filePath).
	2.	Include the New File:

include '_pre_xml.php';


	3.	Update Allowed Formats:
	•	Add 'xml' to the $allowedFormats array in your_script.php.
	4.	Use the Script:

php your_script.php --format=xml



#### Customizing Data Extraction

	•	Modify the existing conversion functions to adapt to your data structure.
	•	Ensure that the functions return data in the format:

[$serializedItineraries, $serializedTaxonomies, $post_title, $post_excerpt]



#### Error Handling

	•	Invalid Format:

Invalid format specified. Allowed formats are 'json' and 'txt'.


	•	No Files Found:

No files matched the pattern '*.<extension>' in the directory: ./data/<extension>


	•	Missing Required Columns:

One or more required columns not found in the template CSV.


	•	Conversion Function Missing:

Conversion function 'convert_<extension>_to_data' does not exist.



#### Troubleshooting

	•	Script Not Running: Ensure PHP is installed and accessible via the command line.
	•	Permission Issues: Check read/write permissions for data files and directories.
	•	Data Files Not Found: Verify that data files are placed in the correct directory (./data/json/ or ./data/txt/).
	•	Invalid Data Format: Ensure your data files are correctly formatted and match the expected structure.
	•	Function Errors: Make sure all required functions are properly defined and included.

#### License

This project is licensed under the MIT License.

Note: Always back up your data before running scripts that modify or generate files.

For questions or support, please open an issue on the GitHub repository.
