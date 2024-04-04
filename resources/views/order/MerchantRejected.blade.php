<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        h2 {
            color: #555;
            margin-top: 30px;
        }

        p {
            margin-bottom: 10px;
            color: #777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Pesanaan {{ $data->order_id }} Dibatalkan</h1>
        <div class="order-info">
            <h2>Informasi Pesanan</h2>
            <p><strong>Transaksi ID:</strong> {{ $data->order_id }}</p>
            <p><strong>PIN Pesanan:</strong> {{ $data->order_pin }}</p>
            <p><strong>Total Produk:</strong> {{ $data->total_product }}</p>
            <p><strong>Sub Total:Rp.</strong> {{ $data->sub_total }}</p>
            <p><strong>Total Promo:Rp.{{ $data->total_promo }}</p>
            <p><strong>Total Harga:Rp.{{ $data->total_price }}</p>
            <p><strong>Pesanaan Diambil:</strong> {{ $data->taken_date }}</p>
            <p><strong>Pesanaan Dibuat:</strong> {{ $data->created_at }}</p>
        </div>

        <div class="rejected-detail">
            <h2>Rician Pembatalan</h2>
            <p><strong>Alasan Pembatalan:</strong> {{ $data->rejected_reason }}</p>
            <p><strong>Pesan Pembatalan:</strong> {{ $data->rejected_message }}</p>
            <p><strong>Dibatalkan Pada:</strong> {{ $data->updated_at }}</p>
        </div>

        <div class="order-details">
            <h2>Detail Pesanan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Foto Produk</th>
                        <th>Nama Produk</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Promo</th>
                        <th>Total Harga</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data->details as $detail)
                    <tr>
                        <td><img src="{{ $detail->product_photo }}" alt="{{ $detail->product_name }}"></td>
                        <td>{{ $detail->product_name }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>Rp. {{ $detail->price }}</td>
                        <td>Rp. {{ $detail->promo_price }}</td>
                        <td>Rp. {{ $detail->total_price }}</td>
                        <td>{{ $detail->notes }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td>Total </td>
                        <td></td>
                        <td>{{ $data->total_quantity }}</td>
                        <td></td>
                        <td>Rp. {{ $data->total_promo }}</td>
                        <td>Rp. {{ $data->total_price }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="shop-data">
            <h2>Informasi Toko</h2>
            <img src="{{ $data->shop_data->photo }}" alt="Foto Toko">
            <p><strong>Nama Toko:</strong> {{ $data->shop_data->shop_name }}</p>
            <p><strong>Nomor HP:</strong> {{ $data->shop_data->phone_number }}</p>
            <p><strong>Lokasi:</strong> {{ $data->shop_data->benchmark }}</p>
        </div>
    </div>
</body>

</html>
