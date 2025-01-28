<?php
// 解析 URL 请求
$requestUri = trim($_SERVER['REQUEST_URI'], "/");
$parts = explode("/", $requestUri);

if (count($parts) < 4) {
    http_response_code(400);
    die("无效的 GitHub 文件路径");
}

list($user, $repo, $branch) = array_slice($parts, 0, 3);
$filePath = implode("/", array_slice($parts, 3));

// GitHub raw 文件地址
$githubRawUrl = "https://raw.githubusercontent.com/$user/$repo/$branch/$filePath";

// 获取 GitHub 文件内容
$fileContent = @file_get_contents($githubRawUrl);
if ($fileContent === false) {
    http_response_code(404);
    die("文件未找到或无法访问");
}

// 获取 GitHub 文件的 Content-Type
$headers = get_headers($githubRawUrl, 1);
$contentType = isset($headers["Content-Type"]) ? $headers["Content-Type"] : "application/octet-stream";

// 设置 Content-Type 以便浏览器自行决定是否预览或下载
header("Content-Type: " . $contentType);
echo $fileContent;
exit;
?>
