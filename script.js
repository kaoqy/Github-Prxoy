document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('url-input');
    const fetchButton = document.getElementById('fetch-button');
     const loadingDiv = document.getElementById('loading');
    const contentPreview = document.getElementById('content-preview');
     const errorMessageDiv = document.getElementById('error-message');

    fetchButton.addEventListener('click', function() {
        const url = urlInput.value.trim();
        if (!url) {
            alert('请输入 GitHub Raw URL！');
            return;
        }

       loadingDiv.style.display = 'block';
     errorMessageDiv.style.display = 'none'
     contentPreview.innerHTML = '';
        
        // 使用 fetch 发送请求到服务器
             fetch(`/proxy?url=${encodeURIComponent(url)}`)
            .then(response => {
               loadingDiv.style.display = 'none';
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                 }
                return response.text(); 
            })
             .then(data => {
                 contentPreview.textContent = data
             })
            .catch(error =>{
                   errorMessageDiv.style.display = 'block';
                    errorMessageDiv.textContent = `错误: ${error.message}`
                });
       
    });
});
