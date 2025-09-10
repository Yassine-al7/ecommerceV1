@extends('layouts.app')

@section('title', 'فواتيري')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">فواتيري</h1>
            <p class="text-gray-600">تتبع الطلبات المسلّمة وحالة الدفع الخاصة بها</p>
        </div>

        <!-- Cartes de Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">الطلبات المُسلَّمة</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $orders->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-coins text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">الإيرادات</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format((float)$totalRevenue, 2) }} MAD</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">الأرباح المدفوعة</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format((float)$totalBeneficesPayes, 2) }} MAD</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">الأرباح المستحقة</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format((float)$totalBeneficesNonPayes, 2) }} MAD</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des Factures -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200"></div>

            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المرجع
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    العميل
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    تاريخ التسليم
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المبلغ
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الربح
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    حالة الدفع
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    إجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $order->reference }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->nom_client }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($order->updated_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ number_format((float)$order->prix_commande, 2) }} MAD
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600">
                                        {{ number_format((float)$order->marge_benefice, 2) }} MAD
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($order->facturation_status == 'payé')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>مدفوع
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>قيد الانتظار
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('seller.invoices.show', $order->id) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye mr-1"></i>عرض
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            @else
                <!-- État vide -->
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-file-invoice text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-800 mb-2">لا توجد فواتير</h3>
                    <p class="text-gray-600">لا توجد طلبات مُسلَّمة للفوترة حتى الآن.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
