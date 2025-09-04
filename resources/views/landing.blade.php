@extends('layouts.landing')

@section('title', 'منصة التجارة الإلكترونية - ابدأ عملك التجاري عبر الإنترنت')

@section('content')
<!-- Hero Section -->
<section class="gradient-bg hero-pattern min-h-screen flex items-center relative overflow-hidden pt-20">
    <!-- Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-4 md:left-10 w-48 md:w-72 h-48 md:h-72 bg-white opacity-10 rounded-full blur-3xl floating-animation"></div>
        <div class="absolute bottom-20 right-4 md:right-10 w-64 md:w-96 h-64 md:h-96 bg-purple-300 opacity-10 rounded-full blur-3xl floating-animation" style="animation-delay: -3s;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <!-- Left Content -->
            <div class="text-white text-center lg:text-right">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    انضم إلى عالم
                    <span class="gradient-text bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                        التجارة الإلكترونية
                    </span>
                </h1>
                <p class="text-lg sm:text-xl lg:text-2xl text-blue-100 mb-8 leading-relaxed">
                    مع منصتنا، يمكنك البدء بـ <strong>0 درهم</strong> والتوصيل على حسابنا
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('register') }}" class="btn-primary text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold text-center">
                        <i class="fas fa-rocket ml-2"></i>
                        ابدأ الآن
                    </a>
                    <a href="#features" class="btn-secondary text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold text-center">
                        <i class="fas fa-info-circle ml-2"></i>
                        اعرف المزيد
                    </a>
                </div>
            </div>

            <!-- Right Content - Illustration -->
            <div class="relative mt-8 lg:mt-0">
                <div class="relative z-10">
                    <div class="bg-white/10 backdrop-blur-lg rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-white/20">
                        <div class="text-center">
                            <div class="w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full mx-auto mb-4 sm:mb-6 flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-2xl sm:text-4xl text-white"></i>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold text-white mb-3 sm:mb-4">ابدأ عملك التجاري</h3>
                            <p class="text-blue-100 mb-4 sm:mb-6 text-sm sm:text-base">أنشئ متجرك الإلكتروني في دقائق</p>
                            <div class="grid grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm">
                                <div class="bg-white/10 rounded-lg p-2 sm:p-3">
                                    <i class="fas fa-check text-green-400 mb-1 sm:mb-2"></i>
                                    <p class="text-white">مجاني</p>
                                </div>
                                <div class="bg-white/10 rounded-lg p-2 sm:p-3">
                                    <i class="fas fa-shipping-fast text-blue-400 mb-1 sm:mb-2"></i>
                                    <p class="text-white">توصيل مجاني</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Floating elements -->
                <div class="absolute -top-2 sm:-top-4 -right-2 sm:-right-4 w-12 sm:w-20 h-12 sm:h-20 bg-yellow-400 rounded-full opacity-80 pulse-animation"></div>
                <div class="absolute -bottom-2 sm:-bottom-4 -left-2 sm:-left-4 w-10 sm:w-16 h-10 sm:h-16 bg-green-400 rounded-full opacity-80 pulse-animation" style="animation-delay: -1s;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-12 sm:py-16 lg:py-20 dark-mode-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold dark-mode-text mb-4">لماذا تختار منصتنا؟</h2>
            <p class="text-base sm:text-lg lg:text-xl dark-mode-text-secondary max-w-3xl mx-auto">
                نقدم لك فرص ربح مميزة بفضل أسعارنا التنافسية،
                وسرعة تنفيذ الطلبات، وثقتنا الكبيرة في خدماتنا.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Feature 1 -->
            <div class="feature-card dark-mode-card p-6 sm:p-8 rounded-2xl shadow-lg dark-mode-border">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 sm:mb-6">
                    <i class="fas fa-check text-lg sm:text-2xl text-green-600"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold dark-mode-text mb-3 sm:mb-4">مجاني</h3>
                <p class="dark-mode-text-secondary text-sm sm:text-base">توصيل مجاني إذا لم تصل الطلبية. لا يوجد أي مخاطر مالية عليك.</p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card dark-mode-card p-6 sm:p-8 rounded-2xl shadow-lg dark-mode-border">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 sm:mb-6">
                    <i class="fas fa-dollar-sign text-lg sm:text-2xl text-green-600"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold dark-mode-text mb-3 sm:mb-4">ربح عالي</h3>
                <p class="dark-mode-text-secondary text-sm sm:text-base">منتجات مباشرة من المصنع. حدد سعرك الخاص وزد من أرباحك.</p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card dark-mode-card p-6 sm:p-8 rounded-2xl shadow-lg dark-mode-border">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4 sm:mb-6">
                    <i class="fas fa-lightbulb text-lg sm:text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold dark-mode-text mb-3 sm:mb-4">شفافية</h3>
                <p class="dark-mode-text-secondary text-sm sm:text-base">شاهد أرباحك وحالة طلباتك بوضوح في الوقت الفعلي.</p>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card dark-mode-card p-6 sm:p-8 rounded-2xl shadow-lg dark-mode-border">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4 sm:mb-6">
                    <i class="fas fa-star text-lg sm:text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold dark-mode-text mb-3 sm:mb-4">سهولة</h3>
                <p class="dark-mode-text-secondary text-sm sm:text-base">اعمل من هاتفك. واجهة بديهية وسهلة الاستخدام.</p>
            </div>

            <!-- Feature 5 -->
            <div class="feature-card dark-mode-card p-6 sm:p-8 rounded-2xl shadow-lg dark-mode-border">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4 sm:mb-6">
                    <i class="fas fa-shield-alt text-lg sm:text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold dark-mode-text mb-3 sm:mb-4">سرية</h3>
                <p class="dark-mode-text-secondary text-sm sm:text-base">معلومات عملائك تبقى سرية ومحمية.</p>
            </div>

            <!-- Feature 6 -->
            <div class="feature-card dark-mode-card p-6 sm:p-8 rounded-2xl shadow-lg dark-mode-border">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4 sm:mb-6">
                    <i class="fas fa-lock text-lg sm:text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold dark-mode-text mb-3 sm:mb-4">أمان</h3>
                <p class="dark-mode-text-secondary text-sm sm:text-base">تحويلات بنكية سريعة وآمنة. مدفوعاتك محمية.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="gradient-bg py-12 sm:py-16 lg:py-20 relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-10 left-4 md:left-10 w-48 md:w-64 h-48 md:h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-4 md:right-10 w-64 md:w-80 h-64 md:h-80 bg-purple-300 opacity-5 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center">
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 sm:mb-6">
                اشترك الآن وابدأ الربح بدون أي جهد
            </h2>
            <p class="text-base sm:text-lg lg:text-xl text-blue-100 mb-6 sm:mb-8 max-w-3xl mx-auto">
                انضم إلى آلاف البائعين الذين حولوا أعمالهم بالفعل مع منصة التجارة الإلكترونية الخاصة بنا.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="btn-primary text-white px-6 sm:px-8 lg:px-10 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold">
                    <i class="fas fa-user-plus ml-2"></i>
                    إنشاء حساب
                </a>
                <a href="{{ route('login') }}" class="btn-secondary text-white px-6 sm:px-8 lg:px-10 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold">
                    <i class="fas fa-sign-in-alt ml-2"></i>
                    تسجيل الدخول
                </a>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-12 sm:py-16 lg:py-20 dark-mode-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div class="text-center lg:text-right">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold dark-mode-text mb-4 sm:mb-6">جيب غير الكوموند</h2>
                <p class="text-base sm:text-lg dark-mode-text-secondary mb-4 sm:mb-6">
                    التوجاد والتوصيل علينا و فلوسك توصل ليك و إلا تلغى الطلب ؟ ما كاين حتى مشكل فابور 100%.
                </p>
                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-center justify-center lg:justify-start">
                        <i class="fas fa-check-circle text-green-500 ml-3"></i>
                        <span class="dark-mode-text">إدارة شاملة للطلبات</span>
                    </div>
                    <div class="flex items-center justify-center lg:justify-start">
                        <i class="fas fa-check-circle text-green-500 ml-3"></i>
                        <span class="dark-mode-text">توصيل سريع وموثوق</span>
                    </div>
                    <div class="flex items-center justify-center lg:justify-start">
                        <i class="fas fa-check-circle text-green-500 ml-3"></i>
                        <span class="dark-mode-text">دعم العملاء 24/7</span>
                    </div>
                </div>
                <div class="mt-6 sm:mt-8">
                    <a href="{{ route('register') }}" class="btn-primary text-white px-6 sm:px-8 py-3 rounded-lg text-base sm:text-lg font-semibold">
                        <i class="fas fa-arrow-left ml-2"></i>
                        دخول المنصة
                    </a>
                </div>
            </div>
            <div class="relative mt-8 lg:mt-0">
                <div class="dark-mode-card rounded-2xl shadow-2xl p-6 sm:p-8 dark-mode-border">
                    <div class="text-center">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mx-auto mb-4 sm:mb-6 flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-2xl sm:text-3xl text-white"></i>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold dark-mode-text mb-3 sm:mb-4">واجهة الهاتف المحمول</h3>
                        <p class="dark-mode-text-secondary mb-4 sm:mb-6 text-sm sm:text-base">أدر عملك من هاتفك</p>
                        <div class="grid grid-cols-3 gap-3 sm:gap-4">
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-2 sm:p-3 text-center">
                                <i class="fas fa-chart-line text-blue-500 mb-1 sm:mb-2 text-sm sm:text-base"></i>
                                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-200">التحليلات</p>
                            </div>
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-2 sm:p-3 text-center">
                                <i class="fas fa-shopping-bag text-green-500 mb-1 sm:mb-2 text-sm sm:text-base"></i>
                                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-200">الطلبات</p>
                            </div>
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-2 sm:p-3 text-center">
                                <i class="fas fa-wallet text-purple-500 mb-1 sm:mb-2 text-sm sm:text-base"></i>
                                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-200">المدفوعات</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-12 sm:py-16 lg:py-20 dark-mode-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold dark-mode-text mb-4">اتصل بنا</h2>
            <p class="text-base sm:text-lg lg:text-xl dark-mode-text-secondary">هل لديك أسئلة؟ فريقنا هنا لمساعدتك.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <div class="text-center">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-phone text-lg sm:text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold dark-mode-text mb-2">الهاتف</h3>
                <p class="dark-mode-text-secondary text-sm sm:text-base" dir="ltr">+212 620 011108</p>
            </div>

            <div class="text-center">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-envelope text-lg sm:text-2xl text-green-600"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold dark-mode-text mb-2">البريد الإلكتروني</h3>
                <p class="dark-mode-text-secondary text-sm sm:text-base">affilook.ma@gmail.com</p>
            </div>

            <div class="text-center sm:col-span-2 lg:col-span-1">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-clock text-lg sm:text-2xl text-purple-600"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold dark-mode-text mb-2">ساعات العمل</h3>
                <p class="dark-mode-text-secondary text-sm sm:text-base">دعم 24/7</p>
            </div>
        </div>
    </div>
</section>
@endsection
