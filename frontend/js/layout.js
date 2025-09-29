// loadPage - phiên bản đã fix, giữ type module, chèn script/style đúng chỗ
async function loadPage(url) {
  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error("Không thể tải trang: " + res.status);
    const html = await res.text();

    const container = document.getElementById("app-content");
    if (!container) throw new Error("Không tìm thấy #app-content");

    // Thêm HTML vào container (tạm)
    container.innerHTML = html;

    // --- XÓA các resource động cũ ---
    document
      .querySelectorAll("script[data-dynamic]")
      .forEach((el) => el.remove());
    document
      .querySelectorAll("link[data-dynamic-css]")
      .forEach((el) => el.remove());
    document
      .querySelectorAll("style[data-dynamic-style]")
      .forEach((el) => el.remove());

    // --- CHUYỂN các <link rel="stylesheet"> trong container -> head ---
    const links = Array.from(
      container.querySelectorAll('link[rel="stylesheet"]')
    );
    links.forEach((oldLink) => {
      const newLink = document.createElement("link");
      newLink.rel = "stylesheet";
      newLink.href = oldLink.href;
      newLink.setAttribute("data-dynamic-css", "true");
      document.head.appendChild(newLink);
      oldLink.remove(); // tránh lặp trong container
    });

    // --- CHUYỂN các <style> nội tuyến trong container -> head ---
    const styles = Array.from(container.querySelectorAll("style"));
    styles.forEach((oldStyle) => {
      const newStyle = document.createElement("style");
      newStyle.innerHTML = oldStyle.innerHTML;
      newStyle.setAttribute("data-dynamic-style", "true");
      document.head.appendChild(newStyle);
      oldStyle.remove();
    });

    // --- XỬ LÝ <script> trong container ---
    // Lấy tất cả script cũ (sắp xếp theo thứ tự xuất hiện)
    const oldScripts = Array.from(container.querySelectorAll("script"));

    // Function load external script sequentially (giữ thứ tự)
    const loadExternalScript = (src, type) => {
      return new Promise((resolve, reject) => {
        const s = document.createElement("script");
        s.src = src;
        if (type) s.type = type;
        s.async = false; // để giữ thứ tự
        s.setAttribute("data-dynamic", "true");
        s.onload = () => resolve();
        s.onerror = (e) => reject(new Error("Không load được script: " + src));
        // external scripts append to body so they run in global context
        document.body.appendChild(s);
      });
    };

    // Tạo một chuỗi promise để load external scripts theo thứ tự
    for (const oldScript of oldScripts) {
      if (oldScript.src) {
        const type = oldScript.type || null; // giữ nguyên type nếu có (vd: module)
        await loadExternalScript(oldScript.src, type);
      } else {
        // Inline script: tạo thẻ script mới và append vào container (sau nội dung)
        const newScript = document.createElement("script");
        // giữ nguyên type (module hoặc not)
        if (oldScript.type) newScript.type = oldScript.type;
        // dùng innerHTML để giữ nguyên nội dung gốc
        newScript.innerHTML = oldScript.innerHTML;
        newScript.setAttribute("data-dynamic", "true");
        // Append vào container để script có thể tương tác với DOM con vừa load
        container.appendChild(newScript);
        // Nếu script là module, browser sẽ import và chạy; nếu thường, nó cũng chạy ngay.
      }
      // sau khi xử lý, xóa oldScript (đã không cần trong container)
      oldScript.remove();
    }

    // --- HOÀN TẤT ---
    return true;
  } catch (err) {
    console.error("Lỗi khi tải trang:", err);
    const container = document.getElementById("app-content");
    if (container) {
      container.innerHTML =
        '<p class="text-red-500">Không thể tải nội dung.</p>';
    }
    throw err;
  }
}
