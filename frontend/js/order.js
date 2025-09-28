import { fetchWithTokenRetry } from "http://127.0.0.1:5501/frontend/js/authFetch.js";

const statusColors = {
  Pending: "bg-yellow-200 text-yellow-800",
  Confirmed: "bg-blue-200 text-blue-800",
  Shipped: "bg-green-200 text-green-800",
  Delivered: "bg-green-500 text-green-100",
  Cancelled: "bg-red-500 text-red-100",
};

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("vi-VN");
}

function formatCurrency(amount) {
  return Number(amount).toLocaleString("vi-VN") + " Đ";
}

document.getElementById("btnFilter").addEventListener("click", function () {
  const keyword = document.getElementById("txtSearch").value.trim();
  const status = document.getElementById("statusFilter").value;
  loadOrders(keyword, status);
});

async function loadOrders(keyword = "", status = "") {
  try {
    const params = new URLSearchParams();
    if (keyword) params.append("keyword", keyword);
    if (status) params.append("status", status);

    const response = await fetchWithTokenRetry(
      `http://localhost:8000/api/orders/user?${params.toString()}`
    );
    const orders = await response.json();

    const container = document.querySelector("#orderGrid");
    container.innerHTML = "";

    orders.forEach((order) => {
      const statusClass =
        statusColors[order.orderstatus] || "bg-gray-400 text-gray-600";

      const html = `
      <div class="order-card bg-white rounded-lg shadow transition-all duration-300 cursor-pointer" data-id="${
        order.id
      }">
        <div class="p-6 cursor-pointer">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <div class="flex items-center mb-2">
                <span class="status-dot ${statusClass}"></span>
                <span class="font-medium ${statusClass}">${
        order.orderstatus
      }</span>
                <span class="mx-2 text-gray-400">|</span>
                <span class="text-gray-500">Mã đơn: #${String(order.id)}</span>
              </div>
              <h3 class="text-lg font-semibold text-gray-800">
                Tổng tiền:
                <span class="text-blue-600 font-bold">${formatCurrency(
                  order.totalAmount
                )}</span>
              </h3>
              <p class="text-gray-600">Ngày đặt: ${formatDate(
                order.created_at
              )}</p>
            </div>
            <div class="text-right flex flex-col items-end gap-2">
          <button 
            class="btnCancel bg-red-600 text-white px-3 py-1 rounded disabled:opacity-50" 
            ${order.orderstatus === "Pending" ? "" : "disabled"}>
            Hủy đơn hàng
          </button>
          <p class="text-sm text-gray-500">Click để xem chi tiết</p>
        </div>
          </div>
        </div>
      </div>
      `;

      container.insertAdjacentHTML("beforeend", html);
    });

    document.querySelectorAll(".btnCancel").forEach((btn) => {
      btn.addEventListener("click", async function (e) {
        e.stopPropagation();

        const orderCard = e.target.closest(".order-card");
        const orderId = orderCard.dataset.id;

        const confirmed = confirm("Bạn có chắc chắn muốn hủy đơn hàng này?");
        if (!confirmed) return;

        try {
          const response = await fetch(
            `http://localhost:8000/api/orders/${orderId}`,
            {
              method: "PUT",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                orderstatus: "Cancelled",
              }),
            }
          );

          if (!response.ok) {
            alert("Hủy đơn hàng thất bại!");
            return;
          }

          alert("Hủy đơn hàng thành công!");
          loadOrders("", "");
        } catch (error) {
          console.error("Lỗi khi hủy đơn hàng:", error);
          alert("Lỗi kết nối, vui lòng thử lại.");
        }
      });
    });

    container.addEventListener("click", function (e) {
      const card = e.target.closest(".order-card");
      if (card) {
        const orderId = card.dataset.id;
        window.location.href = `../client/orderDetails.html?id=${orderId}`;
      }
    });
  } catch (error) {
    console.error("Lỗi khi load đơn hàng:", error);
  }
}

// loadOrders("", "");
