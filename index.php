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

// 构造 GitHub raw 文件地址
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

// 允许浏览器预览的文件类型
$previewTypes = [
    "text/plain", "text/html", "text/css", "text/javascript",
    "application/javascript", "application/json", "image/png",
    "image/jpeg", "image/gif", "image/svg+xml", "image/webp"
];

if (in_array($contentType, $previewTypes)) {
    // 允许网页直接预览
    header("Content-Type: " . $contentType);
} else {
    // 其他文件类型强制下载
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . basename($filePath) . "\"");
}

echo $fileContent;
exit;
?>
