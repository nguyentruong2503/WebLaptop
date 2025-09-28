// authFetch.js
export async function fetchWithTokenRetry(url, options = {}) {
  let token = localStorage.getItem('token');
  options.headers = {
    ...options.headers,
    'Authorization': `Bearer ${token}`
  };

  let response = await fetch(url, options);

  if (response.status === 401) {
    // Token hết hạn → thử refresh
    const refreshRes = await fetch('http://127.0.0.1:8000/api/refresh', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    if (refreshRes.ok) {
      const data = await refreshRes.json();
      localStorage.setItem('token', data.access_token);
      options.headers['Authorization'] = `Bearer ${data.access_token}`;
      response = await fetch(url, options);
    } else {
      // Refresh thất bại → chuyển về login
      localStorage.removeItem('token');
      alert('Hết phiên đăng nhập, vui lòng đăng nhập lại.');
      window.location.href = '/frontend/layouts/login-layout.html';
    }
  }

  return response;
}
