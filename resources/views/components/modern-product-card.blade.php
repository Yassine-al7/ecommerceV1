@props(['product', 'showActions' => true, 'userType' => 'seller'])

<div class="modern-product-card bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
    <!-- Image du produit avec badge -->
    <div class="relative h-48 bg-gray-100 overflow-hidden">
        @if($product->image && $product->image !== '/storage/products/default-product.svg')
            @php
                $src = trim($product->image ?? '', '/');
                if (preg_match('#^https?://#i', $src)) {
                    $imageUrl = $src;
                } elseif ($src) {
                    $imageUrl = $product->image; // ex: /storage/products/xxx.jpg
                } else {
                    $imageUrl = null;
                }
            @endphp

            @if(!empty($imageUrl))
                <img id="product-image-{{ $product->id }}" src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="text-gray-400 text-center absolute inset-0 items-center justify-center hidden">
                    <i class="fas fa-image text-4xl mb-2"></i>
                    <p class="text-sm">صورة مفقودة</p>
                </div>
            @else
                <div class="text-gray-400 text-center flex items-center justify-center h-full">
                    <div>
                        <i class="fas fa-image text-4xl mb-2"></i>
                        <p class="text-sm">لا توجد صورة</p>
                    </div>
                </div>
            @endif

        @else
            <div class="text-gray-400 text-center flex items-center justify-center h-full">
                <div>
                    <i class="fas fa-image text-4xl mb-2"></i>
                    <p class="text-sm">لا توجد صورة</p>
                </div>
            </div>
        @endif



        <!-- Badge de visibilité -->
        @if($product->visible !== null)
            <div class="absolute top-3 right-3">
                <span class="px-3 py-1 text-xs font-medium rounded-full shadow-lg
                    {{ $product->visible ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                    {{ $product->visible ? 'مرئي' : 'مخفي' }}
                </span>
            </div>
        @endif
    </div>

    <!-- Section d'informations avec overlay bleu -->
    <div class="relative">
        <!-- Overlay bleu avec informations -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-3">
            <div class="mb-2">
                <!-- Informations de base -->
                <h3 class="font-bold text-base mb-1 line-clamp-1">{{ $product->name }}</h3>
                @if($product->category)
                    <p class="text-blue-100 text-xs">{{ $product->category->name }} / {{ $product->id ?? '01' }}</p>
                @endif
            </div>

                                    <!-- Section couleurs et stock compacte -->
            <div class="flex items-center justify-between">
                @php
                    // Utiliser les couleurs visibles (excluant les couleurs masquées)
                    $couleurs = $product->visible_colors ?? [];
                    $couleurs = array_slice($couleurs, 0, 3); // Limiter à 3 couleurs
                @endphp

                @if(!empty($couleurs))
                    <div class="flex items-center space-x-2">
                        <span class="text-blue-100 text-xs">الألوان</span>
                        <div class="flex space-x-1">
                            @foreach($couleurs as $couleur)
                                @php
                                    $couleurData = is_array($couleur) ? $couleur : ['name' => $couleur, 'hex' => '#cccccc'];
                                    $hex = $couleurData['hex'] ?? '#cccccc';
                                    $colorName = $couleurData['name'] ?? $couleur;
                                @endphp
                                <div class="w-4 h-4 border border-white shadow-sm cursor-pointer color-circle"
                                     style="background-color: {{ $hex }}"
                                     title="{{ $colorName }}"
                                     onclick="changeProductImage({{ $product->id }}, '{{ $colorName }}')">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Product ID compact -->
                @if(!empty($product->id))
                    <div class="flex items-center space-x-2">
                        <span class="text-blue-100 text-xs">ID</span>
                        <span class="bg-blue-800 bg-opacity-80 px-2 py-1 rounded-full text-xs font-bold text-white">
                            #{{ $product->id }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informations de prix et détails -->
        <div class="p-3 bg-white">
            <!-- Prix compact -->
            <div class="flex justify-between items-center mb-2">
                <div class="text-center flex-1">
                    <p class="text-gray-600 text-xs">السعر</p>
                    <p class="font-bold text-base text-gray-800">{{ $product->prix_vente ?? 0 }} درهم</p>
                </div>
                <div class="text-center flex-1">
                    <p class="text-gray-600 text-xs">تمن المقترح للبيع</p>
                    @php
                        $prixAdminArray = $product->prix_admin_array ?? [];
                        $prixAdminDisplay = '';
                        if (count($prixAdminArray) > 1) {
                            $prixAdminDisplay = implode(' - ', $prixAdminArray);
                        } elseif (count($prixAdminArray) == 1) {
                            $prixAdminDisplay = $prixAdminArray[0];
                        } else {
                            $prixAdminDisplay = '0';
                        }
                    @endphp
                    <p class="font-bold text-base text-blue-600">{{ $prixAdminDisplay }} درهم</p>
                </div>
            </div>

            <!-- Marge compacte -->
            @php
                $prixAdminArray = $product->prix_admin_array ?? [];
                $prixVente = $product->prix_vente ?? 0;

                if (!empty($prixAdminArray)) {
                    $margeMin = abs($prixVente - max($prixAdminArray));
                    $margeMax = abs($prixVente - min($prixAdminArray));

                    if (count($prixAdminArray) > 1) {
                        $margeDisplay = "+{$margeMin}";
                        if ($margeMin != $margeMax) {
                            $margeDisplay .= " à +{$margeMax}";
                        }
                        $margeDisplay .= " درهم ربح";
                    } else {
                        $marge = abs($prixVente - $prixAdminArray[0]);
                        $margeDisplay = "+{$marge} درهم ربح";
                    }
                } else {
                    $margeDisplay = "0 درهم ربح";
                }
            @endphp
            @if(!empty($prixAdminArray))
                <div class="text-center mb-2">
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                        {{ $margeDisplay }}
                    </span>
                </div>
            @endif

            <!-- Actions compactes -->
            @if($userType === 'admin' && $showActions)
                <div class="flex space-x-1 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.products.edit', $product) }}"
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-1.5 px-2 rounded text-xs font-medium transition-colors">
                        <i class="fas fa-edit mr-1"></i>تعديل
                    </a>
                    <a href="{{ route('admin.products.assign', $product) }}"
                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-1.5 px-2 rounded text-xs font-medium transition-colors">
                        <i class="fas fa-users mr-1"></i>تعيين
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white text-center py-1.5 px-2 rounded text-xs font-medium transition-colors"
                                onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                            <i class="fas fa-trash mr-1"></i>حذف
                        </button>
                    </form>
                </div>
            @endif

            @if($userType === 'seller' && $showActions)
                <div class="pt-2 border-t border-gray-100">
                    <button onclick="viewDetails({{ $product->id }})"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-1.5 px-2 rounded text-xs font-medium transition-colors">
                        <i class="fas fa-eye mr-1"></i>تفاصيل
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.modern-product-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-product-card:hover {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animation pour les couleurs */
.color-circle {
    transition: all 0.2s ease;
}

.color-circle:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
</style>

<script>
// Données des images par couleur pour ce produit
const productColorImages_{{ $product->id }} = @json($product->color_images ?? []);

function viewDetails(productId) {
    // Fonction pour voir les détails du produit
    window.location.href = `/seller/products/${productId}`;
}

function selectProduct(productId) {
    // Fonction pour sélectionner un produit
    if (confirm('هل تريد إضافة هذا المنتج إلى طلبيتك؟')) {
        // Ici vous pouvez ajouter la logique pour ajouter le produit à la commande
        console.log('Produit sélectionné:', productId);
        // Vous pouvez faire un appel AJAX ou rediriger vers une page de commande
    }
}

function changeProductImage(productId, colorName) {
    const productImages = window[`productColorImages_${productId}`];
    const imageElement = document.getElementById(`product-image-${productId}`);

    if (!productImages || !imageElement) return;

    // Chercher les images pour cette couleur
    const colorData = productImages.find(item => item.color === colorName);

    if (colorData && colorData.images && colorData.images.length > 0) {
        // Changer l'image vers la première image de cette couleur
        imageElement.src = colorData.images[0];

        // Effet de transition
        imageElement.style.opacity = '0.7';
        setTimeout(() => {
            imageElement.style.opacity = '1';
        }, 200);

        console.log(`Image changée pour ${colorName}:`, colorData.images[0]);
    } else {
        // Si pas d'image spécifique, garder l'image principale
        console.log(`Aucune image spécifique trouvée pour ${colorName}`);
    }
}
</script>
