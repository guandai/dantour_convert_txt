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


### 数据转换
这里是一个 提示工程的例子，docx 或者 pdf 的文字 提供给AIGC服务， 生成 json 或者 txt ：

```
我的 数据要生政这样的格式  
'''
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
'''

请转换这样的文件：
'''
日期	行   程	膳食	交通
day1	上海赫尔辛基 

乘机经转机飞往素有“北方威尼斯之称” 瑞典首都--斯德哥尔摩  ，抵达后中文导游会在机场出口迎候大家，随后专车将大家送往酒店休息。	/
/
晚	飞机
巴士
day2	赫尔辛基-斯德哥尔摩

早餐后, 赫尔辛基的市区观光：参观主教堂、东正教堂、西贝柳斯公园、还有历史悠久的南码头露天市场等，午餐后游览松鼠岛，傍晚搭乘游轮前往瑞典首都斯德哥尔摩。	早
午
游轮自助	巴士
游轮
day3	斯德哥尔摩

上午10点多抵达，导游将带您进行一天的游览。著名的峡湾街， 参观：举行诺贝尔奖颁奖晚宴的斯德哥尔摩市政厅(*)，观看威严的皇宫卫兵换岗仪式，别具风情的老城区，午餐后前往闻名北欧的瓦萨沉船博物馆(*)参观，此战船能体现出十七世纪时瑞典人造船的技术与艺术，尤其船上的木雕功力至今仍令人惊悍，除此之外，您还有机会徜徉在著名的皇后大道步行街上，这里的大商场，专卖店一定不会让您空手而归.	早
午
晚	巴士
day4	斯德哥尔摩-卡尔斯塔德-奥斯陆

早餐后，驱车前往卡尔斯塔德小镇，欣赏维纳恩湖，随后继续驱车前往挪威首都奥斯陆
抵达后入住当地酒店	早
午
晚	巴士
day5	奥斯陆-Flam-峡湾小镇

早餐后乘车前往风景迷人的世界最大的峡湾－松娜峡湾(SOGNEFJORD))，乘游船（*）在众多的峡湾中，长204公里、深1308米的松娜峡湾是世界最长、最深的峡湾，是举世无双的景观。随后入住峡湾小镇	早
午
晚	巴士
day6	峡湾小镇-奥斯陆

早餐后，驱车返回奥斯陆沿途欣赏美丽的田园风光，森林，湖泊，牧场，如诗如画，欣赏湖光山色，午餐后，游览维格兰雕塑公园、阿克斯胡斯城堡等，随后入住当地酒店	早
午
晚	巴士
day7	奥斯陆雷克雅未克   参考航班  FI323  1400-1440

早餐后，市区游览。后搭乘飞机前往冰岛，抵达后由司机和导游接机，前往冰岛著名的蓝湖温泉浴*（请带好泳衣裤和浴巾，并且请放在随身行李里）蓝湖是一个富含矿物质的地热海水温泉。白色的湖底是二氧化硅，湖水和水里生长的藻类都呈宝石蓝。冬天，四周一片冰天雪地，而湖里冒着热气。水里的矿物质对皮肤病有特殊疗效。泡在温热的水里，用白色的二氧化碳泥在身上揉搓，会将那旅途劳顿一扫而光。蓝湖虽然是露天的，但因是温泉，一年四季都可接待来访者，即使在雪花飘飞的冬天，坐在湖边，浸泡在湖里，也丝毫不觉得冷。晚餐后返回酒店休息。	早
午
晚	飞机巴士
day8	雷克雅未克

早餐后，全天开始著名的“黄金之旅” 游览盖锡尔间歇喷泉（Geysir） 黄金瀑布（Gullfoss)。后前往辛格维利尔国家公园暨冰岛古议会旧址（Thingvellir，它是冰岛历史上最享负盛名的圣地，亦是国家的摇篮。2005年已被联合国教科文组织列入"世界遗产名录"。古议会会址位于欧亚板块和美洲板块的交界处的断层旁边。据说欧亚板块与美洲板块现在仍以年均2厘米的速度在分离。有时间的话游览冰岛最大的教堂－哈特格里姆斯。	早
午
晚	巴士
day9	雷克雅未克赫尔辛基上海  

早餐后，前往机场，经转机返回上海，结束愉快美好的北欧旅程。	早
/
/	飞机
巴士
day10	上海
抵达上海，团队顺利结束！请将您的护照、登机牌交予领队，以便递交领馆进行销签工作。根据领馆的要求，部分客人可能会被通知前往领馆进行面试销签，请提前做好思想准备，谢谢您的配合！	/	/
'''
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
