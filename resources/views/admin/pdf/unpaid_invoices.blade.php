<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فواتير غير مدفوعة - {{ $seller->name }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111827; }
        .header { text-align: center; margin-bottom: 16px; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 4px; }
        .subtitle { font-size: 12px; color: #6B7280; }
        .meta { font-size: 12px; margin: 8px 0 16px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #E5E7EB; padding: 6px 8px; vertical-align: top; }
        th { background: #F3F4F6; text-align: right; }
        .totals { margin-top: 12px; font-size: 12px; }
        .totals span { display: inline-block; margin-left: 12px; }
    </style>
    </head>
<body>
    <div class="header">
        <div class="title">فواتير غير مدفوعة</div>
        <div class="subtitle">البائع: {{ $seller->name }} | RIB: {{ $seller->rib ?? 'N/A' }}</div>
        <div class="meta">تاريخ الإنشاء: {{ $generatedAt->format('Y-m-d H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>العميل</th>
                <th>المدينة</th>
                <th>المنتجات</th>
                <th>سعر الطلب (MAD)</th>
                <th>هامش الربح (MAD)</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->reference ?? $order->id }}</td>
                    <td>{{ $order->nom_client }}</td>
                    <td>{{ $order->ville }}</td>
                    <td>
                        @php
                            $produits = $order->produits;
                            $lines = [];
                            if (is_string($produits)) {
                                $decoded = json_decode($produits, true);
                                if (is_array($decoded)) {
                                    foreach ($decoded as $p) {
                                        $name = '';
                                        $qty = '';
                                        if (isset($p['product_id'])) {
                                            $product = \App\Models\Product::find($p['product_id']);
                                            $name = $product ? $product->name : 'ID: ' . $p['product_id'];
                                        }
                                        if (isset($p['qty'])) { $qty = ' (x' . $p['qty'] . ')'; }
                                        $lines[] = $name . $qty;
                                    }
                                } else {
                                    $lines[] = $produits;
                                }
                            } elseif (is_array($produits)) {
                                foreach ($produits as $p) {
                                    $lines[] = is_array($p) ? ( ($p['name'] ?? 'Produit') . (isset($p['qty']) ? ' (x'.$p['qty'].')' : '') ) : $p;
                                }
                            } else { $lines[] = $produits; }
                        @endphp
                        {{ implode(' | ', $lines) }}
                    </td>
                    <td>{{ number_format((float)$order->prix_commande, 2) }}</td>
                    <td>{{ number_format((float)$order->marge_benefice, 2) }}</td>
                    <td>{{ $order->facturation_status ?? 'non payé' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">لا توجد طلبات غير مدفوعة</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <span>عدد الطلبات: {{ $totals['count'] }}</span>
        <span>إجمالي المبيعات: {{ number_format((float)$totals['revenue'], 2) }} MAD</span>
        <span>إجمالي هامش الربح: {{ number_format((float)$totals['marge_benefice'], 2) }} MAD</span>
    </div>
</body>
</html>


