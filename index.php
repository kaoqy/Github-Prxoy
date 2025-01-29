<?php
// 解析 URL 请求
$requestUri = trim($_SERVER['REQUEST_URI'], "/");
$parts = explode("/", $requestUri);

// 如果 URL 不是 GitHub 反代路径，则显示 UI
if (count($parts) < 4) {
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GitHub 文件反代</title>
    <style>
        :root {
            --bg-color: #f4f4f4;
            --text-color: #333;
            --card-bg: white;
            --btn-bg: #007bff;
            --btn-hover: #0056b3;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #222;
                --text-color: #f4f4f4;
                --card-bg: #333;
                --btn-bg: #0056b3;
                --btn-hover: #007bff;
            }
        }
        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            text-align: center;
            padding: 20px;
            transition: all 0.3s ease;
        }
        .container {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            margin: auto;
        }
        input {
            width: calc(100% - 20px);
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        button {
            background: var(--btn-bg);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        button:hover {
            background: var(--btn-hover);
        }
        .result {
            margin-top: 20px;
            word-break: break-word;
        }
        a {
            color: var(--btn-bg);
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function generateProxyUrl() {
            let inputUrl = document.getElementById("github-url").value.trim();
            let proxyBaseUrl = window.location.origin + "/";

            try {
                let result = "";

                if (inputUrl.includes("raw.githubusercontent.com")) {
                    let parts = inputUrl.split("raw.githubusercontent.com/")[1].split("/");
                    result = `${proxyBaseUrl}${parts.join("/")}`;
                } else if (inputUrl.includes("github.com") && inputUrl.includes("/blob/")) {
                    let parts = inputUrl.split("github.com/")[1].split("/blob/");
                    let [userRepo, branchAndPath] = parts;
                    let [branch, ...filePath] = branchAndPath.split("/");
                    result = `${proxyBaseUrl}${userRepo}/${branch}/${filePath.join("/")}`;
                } else if (inputUrl.includes("github.com") && inputUrl.includes("/raw/")) {
                    let parts = inputUrl.split("github.com/")[1].split("/raw/");
                    let [userRepo, branchAndPath] = parts;
                    let [branch, ...filePath] = branchAndPath.split("/");
                    result = `${proxyBaseUrl}${userRepo}/${branch}/${filePath.join("/")}`;
                } else {
                    result = "❌ 无效的 GitHub 链接";
                }

                document.getElementById("proxy-url").innerHTML = result.startsWith("❌") ? 
                    `<span style="color: red;">${result}</span>` : 
                    `<a href="${result}" target="_blank">${result}</a>`;
            } catch (error) {
                document.getElementById("proxy-url").innerHTML = "<span style='color: red;'>解析失败，请检查输入的链接格式。</span>";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>GitHub 文件反代</h1>
        <p>输入 GitHub 文件的 URL：</p>
        <input type="text" id="github-url" placeholder="输入 GitHub 文件链接">
        <button onclick="generateProxyUrl()">生成反代 URL</button>
        
        <div class="result">
            <h2>反代 URL：</h2>
            <p id="proxy-url"></p>
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// 处理 GitHub 反代
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

// 直接转发 GitHub 的 Content-Type
header("Content-Type: " . $contentType);
echo $fileContent;
exit;
?>
