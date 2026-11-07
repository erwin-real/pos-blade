<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Receipt #{{ $order->id }}</title>
    <style>
        /* 58mm Receipt Print Stylesheet styling configurations */
        @media print {
            body {
                width: 58mm;
                margin: 0;
                padding: 0;
                font-family: 'Courier New', Courier, monospace; /* Monospace keeps table layout aligned */
                font-size: 12px;
                font-weight: 700;
            }
            .no-print { display: none; } /* Hides interface buttons when sending to device hardware queue */
        }
        
        body {
            width: 58mm;
            margin: 0 auto;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #000; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<!-- 👈 Automatically opens browser layout print modal immediately when page loads -->
<body> 

    <div class="text-center">
        <strong>Miyagios Boutique</strong><br>
        123 Store Street, Deca, Angeles City<br>
        Receipt: #{{ $order->id }}<br>
        Date: {{ $order->created_at->format('M d, Y h:i A') }}
    </div>

    <div class="divider"></div>

    <table>
        <tbody>
            @foreach($order->order_items as $item)
                <tr>
                    <td colspan="2">{{ $item->product->name ?? 'Custom Item' }}</td>
                </tr>
                <tr>
                    <td>{{ $item->quantity }}x ₱{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td><strong>TOTAL:</strong></td>
            <td class="text-right"><strong>₱{{ number_format($order->total_amount, 2) }}</strong></td>
        </tr>
    </table>

    <div class="text-center" style="margin-top: 15px;">
        Thank you for shopping!<br><br>
        &nbsp;
    </div>

    <script>
        // Wait until all images, text, and styles are fully loaded from the cloud server
        window.addEventListener('DOMContentLoaded', () => {
            
            // 1. Trigger the native browser print window modal
            window.print();

            setTimeout(() => {
                window.close();
            }, 2000); // Gives Windows 500ms to register the spool task safely
            
            // 2. This code pauses until the cashier clicks "Print" or "Cancel". 
            // Once they close the dialog box, this line runs and closes the popup tab.
            // window.close();
        });
    </script>

</body>
</html>