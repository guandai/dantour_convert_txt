<?php

/**
 * 验证TXT文件并获取每个 ##[key] 之后的 value
 * @param string $directory 需要检查的文件夹路径
 */
function parseTxtFiles($directory) {
    // 定义允许的题目
    $allowedKeys = [
        '早餐', '午餐', '晚餐', '交通', '题目', '城市', '日期', '提示', '备注', '游轮时刻参考',
        '主题',  '早上', '晚上', '傍晚', '下午', '天数', 
        '参考航班', '活动', '住宿', '天数', '注释', '概述', '摘要'
    ];

    // 遍历指定目录中的所有 .txt 文件
    $files = glob($directory . '/*.txt');
    foreach ($files as $file) {
        echo "正在解析文件: $file\n";
        $content = file_get_contents($file);
        $lines = explode("\n", $content);
        
        $currentKey = null;
        $recordValues = [];
        
        foreach ($lines as $line) {
            // 移除两边的空白字符
            $line = trim($line);

            // 如果是空行，跳过
            if (empty($line)) {
                continue;
            }

            // 检查是否是 ##[key] 格式
            if (preg_match('/^##(.*)$/', $line, $matches)) {
                // 如果有上一个 key 和 value，输出保存的值
                if ($currentKey == null) {
                    echo "!emptyKey: $currentKey\n";
                }

                if ($currentKey != null && empty($recordValues)) {
                    echo "!emptyValue: $currentKey\n";
                }

                // 记录新的 key
                $currentKey = trim($matches[1]);

                // 检查key是否在允许列表中
                if (!in_array($currentKey, $allowedKeys)) {
                    echo "不合法的key: $currentKey in file $file\n";
                    $currentKey = null;
                }

                // 清空记录的 values
                $recordValues = [];
            } else {
                // 记录 value（不是 key 的那一行）
                if ($currentKey !== null) {
                    // 检查是否含有不允许的字符，如 '-' 或 '*'
                    if (strpos($line, '*') !== false) {
                        echo "发现非法字符 '-' 或 '*' in file $file\n";
                    }
                    // 保存该行的值
                    $recordValues[] = $line;
                }
            }
        }

        // 最后一个 key 的值输出
        // if ($currentKey !== null && !empty($recordValues)) {
        //     echo "last Key: $currentKey\n";
        //     echo "last Value: " . implode(',', $recordValues) . "\n";
        // }
    }
}

// 设置要检查的文件夹路径
$directory = '/Users/zhengdai/Library/CloudStorage/OneDrive-Personal/dantour/txt/format'; // 你要检查的目录路径
$aaa = parseTxtFiles($directory);

echo $aaa;
