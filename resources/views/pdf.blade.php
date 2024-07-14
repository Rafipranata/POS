<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Invoice #6</title>

    <style>
        html,
        body {
            margin: 10px;
            padding: 10px;
            font-family: sans-serif;
        }
        h1,h2,h3,h4,h5,h6,p,span,label {
            font-family: sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px !important;
        }
        table thead th {
            height: 28px;
            text-align: left;
            font-size: 16px;
            font-family: sans-serif;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
        }

        .heading {
            font-size: 24px;
            margin-top: 12px;
            margin-bottom: 12px;
            font-family: sans-serif;
        }
        .small-heading {
            font-size: 18px;
            font-family: sans-serif;
        }
        .total-heading {
            font-size: 18px;
            font-weight: 700;
            font-family: sans-serif;
        }
        .order-details tbody tr td:nth-child(1) {
            width: 20%;
        }
        .order-details tbody tr td:nth-child(3) {
            width: 20%;
        }

        .text-start {
            text-align: left;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .company-data span {
            margin-bottom: 4px;
            display: inline-block;
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 400;
        }
        .no-border {
            border: 1px solid #fff !important;
        }
        .bg-blue {
            background-color: #414ab1;
            color: #fff;
        }
    </style>
</head>
<body>

    <table class="order-details">
        <thead>
            <tr>
                <th width="50%" colspan="2">
                    <h2 class="text-start">POS Kasir</h2>
                </th>
                <th width="50%" colspan="2" class="text-end company-data">
                    <span>Invoice Id: #{{ $invoice->id }}</span> <br>
                    <span>Date: {{ \Carbon\Carbon::now()->format('d / m / Y') }}</span> <br>
                    <span>Zip code : 560077</span> <br>
                    <span>Address: Banjardawa, Taman, Pemalang</span> <br>
                </th>
            </tr>
            <tr class="bg-blue">
                <th width="50%" colspan="2">Order Details</th>
                <th width="50%" colspan="2">User Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Order Id:</td>
                <td>{{ $invoice->id }}</td>

                <td>Full Name:</td>
                <td>{{ $invoice->customer_name }}</td>
            </tr>
            <tr>
                <td>Ordered Date:</td>
                <td>{{$invoice->created_at}}</td>

                <td>Email:</td>
                <td>{{ $invoice->customer_email }}</td>
            </tr>

        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th class="no-border text-start heading" colspan="4">
                    Order Items
                </th>
            </tr>
            <tr class="bg-blue">
                
                <th>Menu</th>
                <th>Harga</th>
                <th>Quantity</th>
                <th>Total</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->invoiceProducts as $product)
            <tr>
                
                <td>
                    {{ $product->Product->name}}
                </td>
                <td width="10%">{{ number_format($product->product_price) }}</td>
                <td width="10%">{{ $product->quantity }}</td>
                <td width="15%" class="fw-bold">{{ number_format($product->product_price * $product->quantity) }}</td>
            </tr>
            @endforeach
            
            <tr>
                <td colspan="2" class="total-heading">Total Jumlah :</td>
                <td colspan="2" class="total-heading">Rp {{ number_format($invoice->total_price, 0, ',', '.') }}</td>
            </tr>
            

        </tbody>
    </table>
    

    <br>
    <p class="text-center">
        Terima kasih telah berbelanja di toko kami.
    </p>

</body>
</html>
