<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الطلب - التفاصيل</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto mt-5">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">تفاصيل الطلب {{ $order->reference }}</h1>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">← رجوع</a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">العميل</p>
                    <p class="text-lg text-gray-900">{{ $order->nom_client }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">المدينة</p>
                    <p class="text-lg text-gray-900">{{ $order->ville }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">العنوان</p>
                    <p class="text-lg text-gray-900">{{ $order->adresse_client }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">الهاتف</p>
                    <p class="text-lg text-gray-900">{{ $order->numero_telephone_client }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">سعر الطلب</p>
                    <p class="text-lg text-gray-900">{{ number_format($order->prix_commande, 2) }} MAD</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">الحالة</p>
                    <p class="text-lg text-gray-900">{{ $order->status }}</p>
                </div>
                @if($order->commentaire)
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">ملاحظة</p>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                        <p class="text-lg text-gray-900">{{ $order->commentaire }}</p>
                    </div>
                </div>
                @endif
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-2">المنتجات</p>
                <div class="bg-gray-50 p-4 rounded border">
                    @php
                        $decodedProducts = json_decode($order->produits, true);
                    @endphp

                    @if(is_array($decodedProducts) && count($decodedProducts) > 0)
                        <div class="space-y-3">
                            @foreach($decodedProducts as $produit)
                                @if(is_array($produit) && isset($produit['product_id']) && isset($produit['qty']))
                                    @php
                                        $product = App\Models\Product::find($produit['product_id']);
                                        $productName = $product ? $product->name : 'المنتج غير موجود';
                                        $category = $product && $product->category ? $product->category->name : 'تصنيف غير معروف';
                                        $price = $product ? $product->prix_vente : 0;
                                        $totalPrice = $price * $produit['qty'];
                                    @endphp

                                    <div class="flex items-center justify-between bg-white p-3 rounded border">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $productName }}</h4>
                                            <p class="text-sm text-gray-500">التصنيف: {{ $category }}</p>
                                            <p class="text-sm text-gray-500">سعر الوحدة: {{ number_format($price, 0) }} MAD</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900">الكمية: {{ $produit['qty'] }}</p>
                                            <p class="text-sm text-green-600 font-medium">الإجمالي: {{ number_format($totalPrice, 0) }} MAD</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">لا توجد منتجات أو تنسيق غير صالح</p>
                        <pre class="mt-2 text-xs text-gray-400">{{ $order->produits }}</pre>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection
</body>
</html>


