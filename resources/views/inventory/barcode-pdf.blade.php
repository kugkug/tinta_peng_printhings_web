<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            color: #666;
        }
        
        .barcode-container {
            margin-bottom: 40px;
            page-break-inside: avoid;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
        }
        
        .barcode-item {
            text-align: center;
        }
        
        .barcode-item h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .barcode-wrapper {
            margin: 20px auto;
            display: inline-block;
            padding: 15px;
            background-color: #fff;
            border: 1px solid #e0e0e0;
        }
        
        .barcode-wrapper svg {
            display: block;
            margin: 0 auto;
        }
        
        .sku-text {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            color: #2563eb;
        }
        
        .price-text {
            font-size: 16px;
            color: #059669;
            font-weight: bold;
            margin-top: 10px;
        }

        .brand-text {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #111827;
        }

        .variant-text,
        .shipping-text {
            font-size: 12px;
            color: #4b5563;
            margin-top: 4px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        /* Print optimizations */
        @media print {
            body {
                padding: 10px;
            }
            
            .barcode-container {
                page-break-inside: avoid;
                margin-bottom: 30px;
            }
        }
        
        /* Multiple items per page layout */
        .items-grid {
            display: block;
        }
        
        @media screen and (min-width: 768px) {
            .items-grid .barcode-container {
                width: 48%;
                display: inline-block;
                vertical-align: top;
                margin-right: 2%;
            }
            
            .items-grid .barcode-container:nth-child(2n) {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>{{ $title }}</p>
        <p style="margin-top: 5px;">Generated on: {{ date('F d, Y h:i A') }}</p>
    </div>
    
    <div class="items-grid">
        @foreach($items as $item)
        <div class="barcode-container">
            <div class="barcode-item">
                <h3>{{ $item['name'] }}</h3>
                @if(!empty($item['brand']))
                    <p class="brand-text">Brand: {{ $item['brand'] }}</p>
                @endif
                @if(!empty($item['variant']))
                    <p class="variant-text">Variant: {{ $item['variant'] }}</p>
                @endif
                
                <div class="barcode-wrapper">
                    {!! $item['barcode'] !!}
                </div>
                
                <p class="sku-text">SKU: {{ $item['sku'] }}</p>
                <p class="price-text">Total Price: ₱{{ $item['price'] }}</p>
                <p class="shipping-text">Price w/o Shipping: ₱{{ $item['price_without_shipping_fee'] }}</p>
                <p class="shipping-text">Estimated Shipping Fee: ₱{{ $item['estimated_shipping_fee'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>This barcode is machine-readable and can be scanned using standard barcode scanners.</p>
    </div>
</body>
</html>

