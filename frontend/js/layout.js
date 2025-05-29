function loadPage(url) {
  fetch(url)
    .then((res) => res.text())
    .then((html) => {
      const container = document.getElementById("app-content");
      container.innerHTML = html;

      // ❌ Xóa các script cũ đã được thêm từ lần load trước
      document
        .querySelectorAll("script[data-dynamic]")
        .forEach((script) => script.remove());

      // ✅ Thêm lại các script từ trang vừa load
      const scripts = container.querySelectorAll("script");
      scripts.forEach((oldScript) => {
        const newScript = document.createElement("script");

        if (oldScript.src) {
          newScript.src = oldScript.src;
        } else {
          newScript.textContent = oldScript.textContent;
        }

        newScript.setAttribute("data-dynamic", "true"); // đánh dấu script động
        document.body.appendChild(newScript);
      });
    })
    .catch((err) => {
      console.error("Failed to load page:", err);
      document.getElementById("app-content").innerHTML =
        '<p class="text-red-500">Failed to load content.</p>';
    });
}
