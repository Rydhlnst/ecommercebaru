<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Dikirim</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background-color: #2D5A27; color: #ffffff; padding: 20px; text-align: center; }
        .content { padding: 30px; }
        .footer { background-color: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .btn { display: inline-block; background-color: #2D5A27; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; }
        .order-box { background-color: #f9f9f9; border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .shipping-icon { color: #2D5A27; font-size: 48px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            <div class="shipping-icon">&#128666;</div>
            
            <h2>Pesanan Anda Dikirim!</h2>
            
            <p>Halo {{ $order->customer->first_name ?? 'Pelanggan' }},</p>
            
            <p>Pesanan Anda sedang dalam perjalanan ke alamat Anda.</p>
            
            <div class="order-box">
                <h3>Detail Pengiriman</h3>
                <p><strong>Nomor Pesanan:</strong> #{{ $order->increment_id }}</p>
                <p><strong>Metode Pengiriman:</strong> {{ $order->shipping_method ?? '-' }}</p>
                <p><strong>Estimasi Tiba:</strong> 2-3 hari kerja</p>
            </div>
            
            <p>Anda dapat melacak status pengiriman melalui halaman pesanan Anda.</p>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/account/orders/' . $order->id) }}" class="btn">Lacak Pesanan</a>
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Hak cipta dilindungi.</p>
        </div>
    </div>
</body>
</html>
