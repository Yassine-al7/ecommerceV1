@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête avec actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">إدارة البائعين</h1>
                <p class="text-gray-600 mt-1">إدارة حسابات البائعين على منصتك</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                بائع جديد
            </a>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-store text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي البائعين</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">نشطون</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-ban text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">غير نشطين</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">جدد (7 أيام)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['recent'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">بحث</label>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="الاسم، البريد، المتجر..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>بحث
                    </button>
                    @if(request()->has('search'))
                        <a href="{{ route('admin.users.index') }}"
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times mr-2"></i>إعادة ضبط
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Liste des utilisateurs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    قائمة المستخدمين ({{ $users->total() }})
                </h2>
            </div>

            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المستخدم
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الدور
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الحالة
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    معلومات
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    تاريخ الإنشاء
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    إجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <!-- Utilisateur -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full {{ $user->role === 'admin' ? 'bg-purple-100' : 'bg-blue-100' }} flex items-center justify-center">
                                                    <i class="fas {{ $user->role === 'admin' ? 'fa-user-shield text-purple-600' : 'fa-user text-blue-600' }}"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                @if($user->store_name)
                                                    <div class="text-xs text-blue-600">{{ $user->store_name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Rôle -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-store mr-1"></i>
                                            بائع
                                        </span>
                                    </td>

                                    <!-- Statut -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $user->is_active ?? true ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <i class="fas {{ $user->is_active ?? true ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                            {{ $user->is_active ?? true ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>

                                    <!-- Informations -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($user->phone)
                                            <div class="text-xs text-gray-600">
                                                <i class="fas fa-phone mr-1"></i>{{ $user->phone }}
                                            </div>
                                        @endif
                                        @if($user->rib)
                                            <div class="text-xs text-gray-600 font-mono">
                                                <i class="fas fa-credit-card mr-1"></i>{{ Str::mask($user->rib, '*', 4, -4) }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Date création -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('d/m/Y') }}
                                        <div class="text-xs">{{ $user->created_at->format('H:i') }}</div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.users.show', $user) }}"
                                               class="text-blue-600 hover:text-blue-900" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                               class="text-green-600 hover:text-green-900" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form method="POST"
                                                      action="{{ route('admin.users.toggle-status', $user) }}"
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="text-orange-600 hover:text-orange-900" title="تبديل الحالة">
                                                        <i class="fas {{ $user->is_active ?? true ? 'fa-ban' : 'fa-check' }}"></i>
                                                    </button>
                                                </form>
                                                <form method="POST"
                                                      action="{{ route('admin.users.destroy', $user) }}"
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')"
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-users text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 mb-2">لا يوجد بائعون</h3>
                    <p class="text-gray-600 mb-4">
                        @if(request()->has('search'))
                            لا يوجد بائع يطابق معايير البحث.
                        @else
                            ابدأ بإنشاء بائع جديد.
                        @endif
                    </p>
                    <a href="{{ route('admin.users.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>بائع جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
