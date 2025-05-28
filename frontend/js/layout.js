// layout.js - Load nội dung HTML động vào #app-content
function loadPage(url) {
  fetch(url)
    .then(res => res.text())
    .then(html => {
      const container = document.getElementById('app-content');
      container.innerHTML = html;

      // Thực thi các script trong html vừa load
      const scripts = container.querySelectorAll('script');
      scripts.forEach(oldScript => {
        const newScript = document.createElement('script');
        if (oldScript.src) {
          newScript.src = oldScript.src;
        } else {
          newScript.textContent = oldScript.textContent;
        }
        document.body.appendChild(newScript);
        oldScript.remove();
      });
    })
    .catch(err => {
      console.error('Failed to load page:', err);
      document.getElementById('app-content').innerHTML = '<p class="text-red-500">Failed to load content.</p>';
    });
}
