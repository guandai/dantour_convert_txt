<?php

/**
 * 验证 ./data/format 下所有 JSON 文件并检查是否包含指定的字段
 * @param string $directory 需要检查的文件夹路径
 */
function parseJsonFiles($directory) {
    
    // 定义必须存在的天数数据 key
    $allowedKeysDayMust = [
        '天数', '早餐', '午餐', '晚餐', '交通', '城市', '日期', '活动', '住宿'
    ];
    
    // 定义允许的可选 key
    $allowedKeysDayOptional = [
        '参考航班', '提示', '备注', '游轮时刻参考', '早上', '晚上', '傍晚', '下午', '注释', '摘要', '主题', 
    ];

    // 定义必须存在的tripData key
    $allowedKeysTrip = [
        '题目', '概要'
    ];

    // 遍历指定目录中的所有 .json 文件
    $files = glob($directory . '/*.json');
    foreach ($files as $file) {
        echo "正在解析文件: $file\n";
        $content = file_get_contents($file);
        $jsonData = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON格式错误 in file $file\n";
            continue;
        }

        // 检查 tripData 中的必要字段
        $tripData = $jsonData['tripData'] ?? null;
        if ($tripData) {
            foreach ($allowedKeysTrip as $key) {
                if (!array_key_exists($key, $tripData)) {
                    echo "警告: tripData 中缺少 '$key' in file $file\n";
                }
            }
        } else {
            echo "警告: 文件中没有找到 tripData in file $file\n";
            continue; // 如果没有 tripData，则跳过该文件的进一步检查
        }

        // 检查 daysData 中的字段
        $daysData = $jsonData['daysData'] ?? null;
        if ($daysData) {
            foreach ($daysData as $dayIndex => $day) {
                // 检查必须字段
                foreach ($allowedKeysDayMust as $key) {
                    if (!array_key_exists($key, $day)) {
                        echo "警告: day $dayIndex 中缺少 '$key' in file $file\n";
                    }
                }

                // 检查可选字段是否有非法值
                foreach ($day as $key => $value) {
                    if (!in_array($key, $allowedKeysDayMust) && !in_array($key, $allowedKeysDayOptional)) {
                        echo "无效的key: '$key' in day $dayIndex in file $file\n";
                    }
                }
            }
        } else {
            echo "警告: 文件中没有找到 daysData in file $file\n";
        }
    }
}

// 设置要检查的文件夹路径
$directory = './data/format'; // 你要检查的目录路径
parseJsonFiles($directory);

echo "检查完成\n";
