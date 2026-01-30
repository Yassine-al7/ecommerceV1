@props(['product', 'showActions' => true, 'userType' => 'seller'])

@php
    $allImages = [];
    $mainImage = $product->image;
    if ($mainImage && $mainImage !== '/storage/products/default-product.svg') {
        $allImages[] = $mainImage;
    }

    $colorImagesData = $product->color_images ?? [];
    foreach ($colorImagesData as $colorData) {
        if (isset($colorData['images']) && is_array($colorData['images'])) {
            foreach ($colorData['images'] as $img) {
                // Normaliser le chemin
                $img = str_replace('\\', '/', $img);
                if (!in_array($img, $allImages)) {
                    $allImages[] = $img;
                }
            }
        }
    }

    if (empty($allImages)) {
        $allImages[] = '/storage/products/default-product.svg';
    }
    
    $couleurs = $product->visible_colors ?? [];
@endphp

<div x-data="{ 
    activeIndex: 0, 
    total: {{ count($allImages) }},
    scrollToColor(colorName) {
        const colorImages = @json($product->color_images ?? []);
        const colorData = colorImages.find(c => c.color === colorName || (typeof c.color === 'object' && c.color.name === colorName));
        if (colorData && colorData.images && colorData.images.length > 0) {
            const firstImg = colorData.images[0].replace(/\\/g, '/');
            const images = @json($allImages);
            const index = images.findIndex(img => img.replace(/\\/g, '/') === firstImg);
            if (index !== -1) {
                this.scrollToIndex(index);
            }
        }
    },
    scrollToIndex(index) {
        if (index < 0 || index >= this.total) return;
        this.activeIndex = index;
        const container = this.$refs.gallery;
        container.scrollTo({
            left: container.offsetWidth * index,
            behavior: 'smooth'
        });
    },
    next() {
        this.activeIndex = (this.activeIndex + 1) % this.total;
        this.scrollToIndex(this.activeIndex);
    },
    prev() {
        this.activeIndex = (this.activeIndex - 1 + this.total) % this.total;
        this.scrollToIndex(this.activeIndex);
    }
}" class="modern-product-card bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
    
    <!-- Image du produit avec Galerie -->
    <div class="relative h-48 bg-gray-50 overflow-hidden group">
        <!-- Galerie d'images -->
        <div x-ref="gallery" 
             class="flex h-full overflow-x-auto snap-x snap-mandatory scrollbar-hide scroll-smooth"
             @scroll.debounce.100ms="activeIndex = Math.round($event.target.scrollLeft / $event.target.offsetWidth)">
            @foreach($allImages as $index => $image)
                <div class="flex-shrink-0 w-full h-full snap-start relative">
                    <img src="{{ $image }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                         onerror="this.src='/storage/products/default-product.svg'">
                </div>
            @endforeach
        </div>

        @if(count($allImages) > 1)
            <!-- Flèches de navigation -->
            <button @click="prev()" 
                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-blue-600 w-8 h-8 rounded-full flex items-center justify-center shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 z-10 focus:outline-none">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
            <button @click="next()" 
                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-blue-600 w-8 h-8 rounded-full flex items-center justify-center shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 z-10 focus:outline-none">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>

            <!-- Points de pagination -->
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center space-x-1.5 z-10">
                @foreach($allImages as $index => $image)
                    <button @click="scrollToIndex({{ $index }})" 
                            class="h-1.5 rounded-full transition-all duration-300 focus:outline-none"
                            :class="activeIndex === {{ $index }} ? 'bg-blue-600 w-4' : 'bg-gray-300 w-1.5'"></button>
                @endforeach
            </div>
        @endif

        <!-- Badge de visibilité -->
        @if($product->visible !== null)
            <div class="absolute top-3 left-3 z-10">
                <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm
                    {{ $product->visible ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                    {{ $product->visible ? 'مرئي' : 'مخفي' }}
                </span>
            </div>
        @endif
    </div>

    <!-- Section d'informations -->
    <div class="relative">
        <!-- Overlay bleu avec informations -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 text-white p-4">
            <div class="mb-3">
                <div class="flex justify-between items-start gap-2">
                    <h3 class="font-bold text-base leading-tight line-clamp-1 flex-1">{{ $product->name }}</h3>
                    @if(!empty($product->id))
                        <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-bold">#{{ $product->id }}</span>
                    @endif
                </div>
                @if($product->category)
                    <p class="text-blue-100 text-[11px] mt-0.5 opacity-90">{{ $product->category->name }}</p>
                @endif
            </div>

            <!-- Section couleurs -->
            @if(!empty($couleurs))
                <div class="flex flex-col space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="text-blue-100 text-[10px] uppercase font-bold tracking-wider shrink-0">الألوان</span>
                        <div class="flex flex-wrap gap-1.5 max-h-16 overflow-y-auto custom-scrollbar pr-1">
                            @foreach($couleurs as $couleur)
                                @php
                                    $couleurData = is_array($couleur) ? $couleur : ['name' => $couleur, 'hex' => '#cccccc'];
                                    $hex = $couleurData['hex'] ?? '#cccccc';
                                    if (!str_starts_with($hex, '#')) $hex = '#' . $hex;
                                    $colorName = $couleurData['name'] ?? $couleur;
                                @endphp
                                <div class="w-4 h-4 rounded-sm border border-white/40 shadow-sm cursor-pointer color-circle transition-all duration-200 hover:scale-125 hover:border-white"
                                     style="background-color: {{ $hex }}"
                                     title="{{ $colorName }}"
                                     @click="scrollToColor('{{ $colorName }}')">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Informations de prix et détails -->
        <div class="p-4 bg-white">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="text-center p-2 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-gray-500 text-[10px] uppercase font-bold tracking-tight mb-1">السعر</p>
                    <p class="font-black text-lg text-gray-800">{{ number_format($product->prix_vente ?? 0, 0, '.', ' ') }} <span class="text-xs font-normal">درهم</span></p>
                </div>
                <div class="text-center p-2 rounded-lg bg-blue-50 border border-blue-100">
                    <p class="text-blue-600 text-[10px] uppercase font-bold tracking-tight mb-1">المقترح</p>
                    @php
                        $prixAdminArray = $product->prix_admin_array ?? [];
                        $prixAdminDisplay = count($prixAdminArray) > 1 
                            ? min($prixAdminArray) . '-' . max($prixAdminArray)
                            : (count($prixAdminArray) == 1 ? $prixAdminArray[0] : '0');
                    @endphp
                    <p class="font-black text-lg text-blue-700">{{ $prixAdminDisplay }} <span class="text-xs font-normal">درهم</span></p>
                </div>
            </div>

            <!-- Marge -->
            @php
                $prixVente = $product->prix_vente ?? 0;
                if (!empty($prixAdminArray)) {
                    $margeMin = abs($prixVente - max($prixAdminArray));
                    $margeMax = abs($prixVente - min($prixAdminArray));
                    $margeDisplay = count($prixAdminArray) > 1 && $margeMin != $margeMax
                        ? "+{$margeMin} à +{$margeMax}"
                        : "+{$margeMin}";
                } else {
                    $margeDisplay = "0";
                }
            @endphp
            <div class="flex justify-center mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-700 border border-green-200">
                    <i class="fas fa-coins mr-1.5 text-[9px]"></i>
                    {{ $margeDisplay }} درهم ربح
                </span>
            </div>

            <!-- Actions -->
            @if($userType === 'admin' && $showActions)
                <div class="flex gap-1.5 mt-2">
                    <a href="{{ route('admin.products.edit', $product) }}"
                       class="flex-1 bg-gray-100 hover:bg-blue-600 hover:text-white text-gray-700 text-center py-2 rounded-lg text-xs font-bold transition-all duration-200">
                        <i class="fas fa-edit mr-1"></i>تعديل
                    </a>
                    <a href="{{ route('admin.products.assign', $product) }}"
                       class="flex-1 bg-gray-100 hover:bg-green-600 hover:text-white text-gray-700 text-center py-2 rounded-lg text-xs font-bold transition-all duration-200">
                        <i class="fas fa-users mr-1"></i>تعيين
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-gray-100 hover:bg-red-600 hover:text-white text-gray-700 text-center py-2 rounded-lg text-xs font-bold transition-all duration-200"
                                onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                            <i class="fas fa-trash mr-1"></i>حذف
                        </button>
                    </form>
                </div>
            @endif

            @if($userType === 'seller' && $showActions)
                <div class="mt-2">
                    <button @click="window.location.href='/seller/products/{{ $product->id }}'"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-200 transition-all duration-200">
                        <i class="fas fa-eye mr-2"></i>عرض التفاصيل
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 3px;
    height: 3px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

