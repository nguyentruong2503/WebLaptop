<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4a90e2;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .order-info {
            margin: 15px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .order-info p {
            margin: 5px 0;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            font-weight: bold;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Xác nhận đơn hàng thành công</h1>
    </div>
    
    <div class="content">
        <p>Xin chào {{ $order->fullName }},</p>
        <p>Cảm ơn bạn đã đặt hàng tại HQLaps. Dưới đây là thông tin chi tiết đơn hàng của bạn:</p>
        
        <div class="order-info">
            <h3>Thông tin đơn hàng #{{ $order->id }}</h3>
            <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Trạng thái:</strong> <span class="status">{{ $order->orderStatus }}</span></p>
            
            <h4>Thông tin giao hàng</h4>
            <p><strong>Họ tên:</strong> {{ $order->fullName }}</p>
            <p><strong>Điện thoại:</strong> {{ $order->phone }}</p>
            <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
            
            <h4>Tổng cộng</h4>
            <p class="total-amount">{{ number_format($order->totalAmount, 0, ',', '.') }} VNĐ</p>
        </div>
        
        <p>Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận đơn hàng.</p>
        <p>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ hotline: 1900 1234</p>
        
        <p>Trân trọng,<br>Đội ngũ HQLaps</p>
    </div>
    
    <div class="footer">
        <p>Đây là email tự động, vui lòng không trả lời email này.</p>
        <p>© {{ date('Y') }} HQLaps. Tất cả các quyền được bảo lưu.</p>
    </div>
</body>
</html>