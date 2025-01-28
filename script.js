document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('url-input');
    const fetchButton = document.getElementById('fetch-button');
     const loadingDiv = document.getElementById('loading');
    const contentPreview = document.getElementById('content-preview');
     const errorMessageDiv = document.getElementById('error-message');

    
    function fetchContent(url){
       loadingDiv.style.display = 'block';
         errorMessageDiv.style.display = 'none'
         contentPreview.innerHTML = '';
      
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
      
    }
	  fetchButton.addEventListener('click', function() {
        const url = urlInput.value.trim();
        if (!url) {
            alert('请输入 GitHub URL！');
            return;
        }
          fetchContent(url)
       
    });
	
	 // check if the url param exist in url, if so fetch the url content
    const urlParams = new URLSearchParams(window.location.search);
    const initialUrl = urlParams.get('url');
       if(initialUrl){
	      urlInput.value = initialUrl
	      fetchContent(initialUrl)
      }  
});
