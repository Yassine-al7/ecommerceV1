@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Modifier le Produit</h1>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>

            <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom du produit -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Produit *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie *</label>
                        <select name="categorie_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('categorie_id', $product->categorie_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('categorie_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Couleurs -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Couleurs *</label>
                        <div class="space-y-4">
                            <!-- Couleurs prédéfinies -->
                            <div>
                                <p class="text-xs text-gray-600 mb-3">Couleurs prédéfinies :</p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @php
                                        $predefinedColors = ['Rouge', 'Vert', 'Bleu', 'Jaune', 'Noir', 'Blanc', 'Orange', 'Violet', 'Rose', 'Marron', 'Gris', 'Beige'];
                                        $currentColors = is_string($product->couleur) ? json_decode($product->couleur, true) ?? [] : (is_array($product->couleur) ? $product->couleur : []);
                                    @endphp
                                    @foreach($predefinedColors as $colorName)
                                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                                            <input type="checkbox" name="couleurs[]" value="{{ $colorName }}"
                                                   @checked(in_array($colorName, old('couleurs', $currentColors)))
                                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="w-4 h-4 rounded-full border border-gray-300"
                                                  style="background-color: {{ \App\Helpers\ColorHelper::generateColorFromName($colorName) }}"></span>
                                            <span class="text-sm text-gray-700">{{ $colorName }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Color picker pour couleurs personnalisées -->
                            <div class="border-t pt-4">
                                <p class="text-xs text-gray-600 mb-3">Ajouter une couleur personnalisée :</p>
                                <div class="flex items-center space-x-3">
                                    <input type="color" id="customColorPicker"
                                           class="w-16 h-12 border-2 border-gray-300 rounded-lg cursor-pointer shadow-sm">
                                    <input type="text" id="customColorName" placeholder="Nom de la couleur"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" onclick="addCustomColor()"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Ajouter
                                    </button>
                                </div>
                            </div>

                            <!-- Couleurs personnalisées ajoutées -->
                            <div id="customColorsContainer" class="space-y-2">
                                @if(is_array($currentColors))
                                    @foreach($currentColors as $color)
                                        @if(!in_array($color, $predefinedColors))
                                            <div class="custom-color-item flex items-center space-x-2 p-2 bg-gray-50 rounded-lg">
                                                <span class="w-4 h-4 rounded-full border border-gray-300"
                                                      style="background-color: {{ \App\Helpers\ColorHelper::generateColorFromName($color) }}"></span>
                                                <span class="text-sm text-gray-700">{{ $color }}</span>
                                                <button type="button" onclick="removeCustomColor(this)"
                                                        class="text-red-600 hover:text-red-800 ml-auto">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <input type="hidden" name="couleurs[]" value="{{ $color }}">
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @error('couleurs')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tailles -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tailles Disponibles *</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '38', '40', '42', '44', '46', '48', '50', '52'];
                                $currentSizes = is_string($product->tailles)
                                    ? (json_decode($product->tailles, true) ?? [])
                                    : (is_array($product->tailles) ? $product->tailles : []);
                            @endphp
                            @foreach($sizes as $size)
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="tailles[]" value="{{ $size }}"
                                           @checked(in_array($size, old('tailles', $currentSizes)))
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('tailles')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix Admin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix Admin (MAD) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">MAD</span>
                            <input type="number" name="prix_admin" value="{{ old('prix_admin', $product->prix_admin) }}" step="0.01" min="0" required
                                   class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        @error('prix_admin')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix de Vente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix de Vente (MAD) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">MAD</span>
                            <input type="number" name="prix_vente" value="{{ old('prix_vente', $product->prix_vente) }}" step="0.01" min="0" required
                                   class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        @error('prix_vente')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantité en Stock -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité en Stock *</label>
                        <input type="number" name="quantite_stock" value="{{ old('quantite_stock', $product->quantite_stock) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('quantite_stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Image du Produit</label>
                        @if($product->image)
                            <div class="mb-2">
                                <img src="{{ $product->image }}" alt="Image actuelle" class="w-20 h-20 object-cover rounded-lg border">
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Mettre à Jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gestion des couleurs personnalisées
let customColorCounter = 0;

// Ajouter une couleur personnalisée
function addCustomColor() {
    const colorPicker = document.getElementById('customColorPicker');
    const colorNameInput = document.getElementById('customColorName');
    const container = document.getElementById('customColorsContainer');

    if (!colorNameInput.value.trim()) {
        alert('Veuillez entrer un nom pour la couleur');
        return;
    }

    const colorName = colorNameInput.value.trim();
    const colorHex = colorPicker.value;

    // Créer l'élément de couleur personnalisée
    const colorItem = document.createElement('div');
    colorItem.className = 'custom-color-item flex items-center space-x-2 p-2 bg-gray-50 rounded-lg';
    colorItem.innerHTML = `
        <span class="w-4 h-4 rounded-full border border-gray-300" style="background-color: ${colorHex}"></span>
        <span class="text-sm text-gray-700">${colorName}</span>
        <button type="button" onclick="removeCustomColor(this)" class="text-red-600 hover:text-red-800 ml-auto">
            <i class="fas fa-times"></i>
        </button>
        <input type="hidden" name="couleurs[]" value="${colorName}">
    `;

    container.appendChild(colorItem);

    // Réinitialiser les inputs
    colorNameInput.value = '';
    colorPicker.value = '#000000';

    customColorCounter++;
}

// Supprimer une couleur personnalisée
function removeCustomColor(button) {
    button.closest('.custom-color-item').remove();
}

// Auto-remplir le nom de couleur quand la couleur change
document.getElementById('customColorPicker').addEventListener('change', function() {
    const colorNames = {
        '#ff0000': 'Rouge', '#00ff00': 'Vert', '#0000ff': 'Bleu', '#ffff00': 'Jaune',
        '#ff00ff': 'Magenta', '#00ffff': 'Cyan', '#000000': 'Noir', '#ffffff': 'Blanc',
        '#ffa500': 'Orange', '#800080': 'Violet', '#ffc0cb': 'Rose', '#a52a2a': 'Marron',
        '#ff4500': 'Orange-Rouge', '#32cd32': 'Lime', '#4169e1': 'Royal Blue', '#ffd700': 'Or'
    };

    const colorNameInput = document.getElementById('customColorName');
    const selectedColor = this.value;

    if (colorNames[selectedColor]) {
        colorNameInput.value = colorNames[selectedColor];
    }
});

// Validation du formulaire
document.querySelector('form').addEventListener('submit', function(e) {
    const selectedColors = document.querySelectorAll('input[name="couleurs[]"]:checked');
    const customColors = document.querySelectorAll('#customColorsContainer input[name="couleurs[]"]');

    if (selectedColors.length === 0 && customColors.length === 0) {
        e.preventDefault();
        alert('Veuillez sélectionner au moins une couleur');
        return;
    }
});

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Formulaire d\'édition des couleurs initialisé');

    // Vérifier qu'au moins une couleur est sélectionnée
    const selectedColors = document.querySelectorAll('input[name="couleurs[]"]:checked');
    const customColors = document.querySelectorAll('#customColorsContainer input[name="couleurs[]"]');

    if (selectedColors.length === 0 && customColors.length === 0) {
        console.log('Aucune couleur sélectionnée, sélection automatique de la première couleur prédéfinie');
        const firstCheckbox = document.querySelector('input[name="couleurs[]"]');
        if (firstCheckbox) {
            firstCheckbox.checked = true;
        }
    }
});
</script>
@endsection


