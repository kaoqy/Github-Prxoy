const express = require('express');
const axios = require('axios');
const path = require('path');

const app = express();
const PORT = 3000;

app.use(express.static(path.join(__dirname)));

app.get('/proxy', async (req, res) => {
    let url = req.query.url;
    if (!url) {
        return res.status(400).send("URL 参数不能为空");
    }

    let githubRawUrl;

    try {
       let parsedUrl;
       
       try{
          parsedUrl = new URL(url)
       }catch(error){
          return res.status(400).send(`Invalid URL: ${error.message}`);
       }

        if (url.includes('raw.githubusercontent.com')) {
            // 处理直接提供的 raw URL
            githubRawUrl = url;
        } else if(url.includes('github.com')) {
           //处理 github blob 或者 github raw 情况

             const pathSegments = parsedUrl.pathname.split('/').filter(Boolean)
          if(pathSegments.length < 3){
             return res.status(400).send('Invalid GitHub URL format')
          }
           const user = pathSegments[0];
           const repo = pathSegments[1];
          let branch;
          let filePath;
           if(pathSegments[2] === 'blob'){
                 branch = pathSegments[3];
                 filePath = pathSegments.slice(4).join('/')
           }else if(pathSegments[2] === 'raw'){
                if(pathSegments[3] === 'refs' && pathSegments[4] === 'heads'){
                    branch = pathSegments[5];
                     filePath = pathSegments.slice(6).join('/');
                }else{
                   branch = pathSegments[3];
                    filePath = pathSegments.slice(4).join('/')
                }

           }else{
                return  res.status(400).send('Invalid GitHub URL format')
           }
          githubRawUrl = `https://raw.githubusercontent.com/${user}/${repo}/${branch}/${filePath}`
           
        } else{
            return res.status(400).send("Invalid GitHub URL!")
        }

        const  response = await axios.get(githubRawUrl, {
              responseType: 'text',
        });
        // 设置 Content-Type 并返回响应
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
