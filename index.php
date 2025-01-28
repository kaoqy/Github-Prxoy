<?php

// 获取 URL 中的路径部分，去掉开头的斜杠
$path = ltrim($_SERVER['REQUEST_URI'], '/');

// 如果路径为空，则显示欢迎页面
if (empty($path)) {
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GitHub Raw 内容代理</title>
        <style>
            body {
                font-family: sans-serif;
                margin: 2em auto;
                max-width: 800px;
                padding: 0 20px;
                line-height: 1.6;
            }
            h1, h2 {
                color: #333;
            }
            ul {
                padding-left: 20px;
            }
            li {
                margin-bottom: 10px;
            }
             a {
               color: #007bff;
               text-decoration: none;
             }
             a:hover {
               text-decoration: underline;
            }
               .center {
                 text-align: center;
               }
        </style>
    </head>
    <body>
        <div class="center">
            <h1>欢迎使用 GitHub Raw 内容代理</h1>
             <p>本代理用于访问 GitHub raw 内容, 你可以通过本代理访问 GitHub 的 raw 文件。</p>
        </div>
        
        <h2>使用方法:</h2>
         <p> 你只需要在代理后面添加github的raw路径即可，例如: </p>
        <ul>
            <li>原始 URL: <a href="https://raw.githubusercontent.com/kaoqy/Image/refs/heads/main/25/1/IMG_5013.jpeg">https://raw.githubusercontent.com/kaoqy/Image/refs/heads/main/25/1/IMG_5013.jpeg</a></li>
             <li>你的代理 URL: <code>你的域名/proxy.php<strong>/kaoqy/Image/main/25/1/IMG_5013.jpeg</strong></code>(例如: <a href="/kaoqy/Image/main/25/1/IMG_5013.jpeg">你的域名/proxy.php/kaoqy/Image/main/25/1/IMG_5013.jpeg</a>) )</li>
        </ul>
        <p><code>/refs/heads</code> 将会自动移除</p>
        <p>本代理基于PHP开发,  <a href="https://github.com/kaoqy/proxy">GitHub地址</a></p>
         
    </body>
    </html>
    HTML;
    exit; // 终止后续代码执行
}

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
