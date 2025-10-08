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

/**
 * Lấy dữ liệu từ JWT token
 * @returns {Object|null} Trả về payload của token hoặc null nếu không hợp lệ
 */
export function getTokenData() {
  const token = localStorage.getItem('token');
  if (!token) return null;

  try {
    // Tách phần payload từ token (phần giữa 2 dấu chấm)
    const base64Url = token.split('.')[1];
    if (!base64Url) return null;
    
    // Thay thế các ký tự đặc biệt để decode base64
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const jsonPayload = decodeURIComponent(
      atob(base64)
        .split('')
        .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
        .join('')
    );

    return JSON.parse(jsonPayload);
  } catch (error) {
    console.error('Lỗi khi giải mã token:', error);
    return null;
  }
}

// Cách sử dụng:
// const tokenData = getTokenData();
// console.log(tokenData); // {sub: 1, name: "Tên người dùng", iat: 1234567890, exp: 1234567890, ...}
