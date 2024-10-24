<?php

/**
 * 验证 ./data/format 下所有 TXT 文件并获取每个 ##[key] 之后的 value
 * @param string $directory 需要检查的文件夹路径
 */
function parseTxtFiles($directory) {
    
    $allowedKeysDayMust = [
        '天数', '早餐', '午餐', '晚餐', '交通', '城市', '日期', '活动', '住宿'
    ];
    
    // 定义允许的keyOptional
    $allowedKeysDayOptional = [
        '参考航班', '提示', '备注', '游轮时刻参考', '早上', '晚上', '傍晚', '下午', '注释', '摘要', '主题', 
    ];

    $allowedKeysTrip = [
        '题目', '概要'
    ];

    // 遍历指定目录中的所有 .txt 文件
    $files = glob($directory . '/*.txt');
    foreach ($files as $file) {
        echo "正在解析文件: $file\n";
        $content = file_get_contents($file);
        $lines = explode("\n", $content);

        $currentKey = null;
        $recordValues = [];
        $titleExists = false;
        $summaryExists = false;
        $invalidKeys = [];
        $emptyValues = false;

        foreach ($lines as $line) {
            // 移除两边的空白字符
            if (strpos($line, '--------------') !== false) {
                continue;
            }
            
            $line = trim($line);

            // 如果是空行，跳过
            if (empty($line)) {
                continue;
            }

            // 检查是否是 ##[key] 格式
            if (preg_match('/^##\s*(.*)\s*$/u', $line, $matches)) {
                // 记录新的 key
                $currentKey = trim($matches[1]);

                // 检查key是否在允许列表中
                if (!in_array($currentKey, $allowedKeys)) {
                    echo "不合法的key: $currentKey in file $file\n";
                    $invalidKeys[] = $currentKey; // 记录无效的key
                    $currentKey = null;
                }

                // 检查是否存在 '题目' 和 '概要'
                if ($currentKey == '题目') {
                    $titleExists = true;
                }
                if ($currentKey == '概要') {
                    $summaryExists = true;
                }

            } else {
                // 清空记录的 values
                $recordValues = [];
                
                // 记录 value（不是 key 的那一行）
                if ($currentKey !== null) {
                    // 检查是否含有不允许的字符，如 '-' 或 '*'
                    if (strpos($line, '*') !== false) {
                        echo "发现非法字符 '*' in file $file\n";
                    }
                    // 检查是否是空值
                    if (empty($line)) {
                        echo "空值警告: $currentKey in file $file\n";
                        $emptyValues = true;
                    }
                    // 保存该行的值
                    $recordValues[] = $line;
                }
            }
        }

        // 检查 '题目' 和 '概要' 是否存在
        if (!$titleExists) {
            echo "警告: '题目' 不存在 in file $file\n";
        }
        if (!$summaryExists) {
            echo "警告: '概要' 不存在 in file $file\n";
        }

        // 输出最终结果
        if (!empty($invalidKeys)) {
            echo "无效的keys: " . implode(',', $invalidKeys) . " in file $file\n";
        }
        if ($emptyValues) {
            echo "发现空值 in file $file\n";
        }
    }
}

// 设置要检查的文件夹路径
$directory = './data/format'; // 你要检查的目录路径
$aaa = parseTxtFiles($directory);

echo $aaa;
