function loadPage(url) {
  fetch(url)
    .then((res) => {
      if (!res.ok) throw new Error("Không thể tải trang: " + res.status);
      return res.text();
    })
    .then((html) => {
      const container = document.getElementById("app-content");
      if (!container) throw new Error("Không tìm thấy #app-content");

      container.innerHTML = html;

      // Xóa các thẻ script/link/style cũ đã thêm động
      document
        .querySelectorAll("script[data-dynamic]")
        .forEach((el) => el.remove());
      document
        .querySelectorAll("link[data-dynamic-css]")
        .forEach((el) => el.remove());
      document
        .querySelectorAll("style[data-dynamic-style]")
        .forEach((el) => el.remove());

      // Thêm lại <link rel="stylesheet"> vào <head>
      container
        .querySelectorAll('link[rel="stylesheet"]')
        .forEach((oldLink) => {
          const newLink = document.createElement("link");
          newLink.rel = "stylesheet";
          newLink.href = oldLink.href;
          newLink.setAttribute("data-dynamic-css", "true");
          document.head.appendChild(newLink);
        });
      // Xóa các <link> trong container để tránh bị lặp
      container
        .querySelectorAll('link[rel="stylesheet"]')
        .forEach((el) => el.remove());

      // Thêm lại các thẻ <style> nội tuyến vào <head>
      container.querySelectorAll("style").forEach((oldStyle) => {
        const newStyle = document.createElement("style");
        newStyle.innerHTML = oldStyle.innerHTML;
        newStyle.setAttribute("data-dynamic-style", "true");
        document.head.appendChild(newStyle);
      });
      // Xóa các <style> trong container
      container.querySelectorAll("style").forEach((el) => el.remove());

      // Thêm lại các <script> vào <body> để thực thi
      container.querySelectorAll("script").forEach((oldScript) => {
        const newScript = document.createElement("script");

        if (oldScript.src) {
          newScript.src = oldScript.src;
          newScript.async = false;
          newScript.setAttribute("data-dynamic", "true");

          // Ví dụ gọi hàm sau khi script tải xong (tùy tình huống)
          newScript.onload = () => {
            if (
              newScript.src.includes("product.js") &&
              typeof fetchProducts === "function"
            ) {
              fetchProducts();
            }
            if (
              newScript.src.includes("order.js") &&
              typeof loadOrders === "function"
            ) {
              loadOrders();
            }
          };
        } else {
          newScript.textContent = oldScript.textContent;
          
          setTimeout(() => {
            if (
              newScript.textContent.includes("fetchProducts") &&
              typeof fetchProducts === "function"
            ) {
              fetchProducts();
            }
          }, 0);
          if(oldScript.getAttribute('type')){
                      newScript.setAttribute("type", "module");

          }
          newScript.setAttribute("data-dynamic", "true");
               

        }

        document.body.appendChild(newScript);
      });
      // Xóa các <script> trong container sau khi đã thêm ra body
      container.querySelectorAll("script").forEach((el) => el.remove());
    })
    .catch((err) => {
      console.error("Lỗi khi tải trang:", err);
      const container = document.getElementById("app-content");
      if (container) {
        container.innerHTML =
          '<p class="text-red-500">Không thể tải nội dung.</p>';
      }
    });
}
