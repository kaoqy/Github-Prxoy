<?php

// 获取 URL 中的路径部分，去掉开头的斜杠
$path = ltrim($_SERVER['REQUEST_URI'], '/');

// 移除 '/refs/heads/' 部分
$new_path = preg_replace('#/refs/heads/#', '/', $path);

// 构建完整的 URL (你的原始 GitHub URL 前缀)
$base_url = 'https://raw.githubusercontent.com';
$target_url = $base_url . '/' . $new_path;

// 使用 cURL 获取远程内容
$ch = curl_init($target_url);

// 设置 cURL 选项
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取内容而不是输出
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟踪重定向 (如果有)

// 执行 cURL 请求
$response = curl_exec($ch);

// 获取 HTTP 状态码
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 获取 content-type
$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

// 关闭 cURL
curl_close($ch);

// 根据 HTTP 状态码处理响应
if ($http_code == 200) {
	// 输出正确的 Content-Type
    if($content_type){
        header('Content-Type: ' . $content_type);
    } 
    // 输出远程内容
    echo $response;
} else {
    // 输出错误信息和状态码
    http_response_code($http_code);
    echo "Error: Could not fetch content from $target_url. HTTP Status Code: $http_code";
}

?>
