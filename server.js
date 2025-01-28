const express = require('express');
const axios = require('axios');
const path = require('path');

const app = express();
const PORT = 3000;

app.use(express.static(path.join(__dirname))); // Serve static files (index.html, script.js, style.css etc)

app.get('/proxy', async (req, res) => {
    const url = req.query.url;

    if (!url) {
        return res.status(400).send("URL参数不能为空");
    }

    try {
        // 从 URL 中提取路径并移除 '/refs/heads/'
        const parsedUrl = new URL(url);
        let path = parsedUrl.pathname.substring(1);
        path = path.replace(/\/refs\/heads\//, '/')

         const targetUrl = `https://raw.githubusercontent.com/${path}`
        const response = await axios.get(targetUrl, {
             responseType: 'text', // 明确指定请求为text
        });

          res.setHeader('Content-Type', response.headers['content-type'] || 'text/plain');
        res.status(response.status).send(response.data);
    } catch (error) {
         console.error("Fetch Error:", error);
         res.status(500).send(`Error: Could not fetch content. ${error.message}`);
    }
});

app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
