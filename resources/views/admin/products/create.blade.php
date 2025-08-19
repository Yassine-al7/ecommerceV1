@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Ajouter un Produit</h1>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>

            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom du produit -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Produit *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
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
                                <option value="{{ $category->id }}" @selected(old('categorie_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('categorie_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Couleur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Couleur *</label>
                        <div class="space-y-3">
                            <!-- Color picker principal -->
                            <div class="flex items-center space-x-3">
                                <input type="color" name="couleur" value="{{ old('couleur', '#000000') }}" required
                                       class="w-16 h-12 border-2 border-gray-300 rounded-lg cursor-pointer shadow-sm">
                                <input type="text" name="couleur_text" value="{{ old('couleur_text') }}" placeholder="Nom de la couleur (ex: Rouge, Bleu)"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Palette de couleurs prédéfinies -->
                            <div>
                                <p class="text-xs text-gray-600 mb-2">Couleurs populaires :</p>
                                <div class="grid grid-cols-8 gap-2">
                                    @php
                                        $popularColors = [
                                            '#ff0000' => 'Rouge', '#00ff00' => 'Vert', '#0000ff' => 'Bleu', '#ffff00' => 'Jaune',
                                            '#ff00ff' => 'Magenta', '#00ffff' => 'Cyan', '#000000' => 'Noir', '#ffffff' => 'Blanc',
                                            '#ffa500' => 'Orange', '#800080' => 'Violet', '#ffc0cb' => 'Rose', '#a52a2a' => 'Marron',
                                            '#ff4500' => 'Orange-Rouge', '#32cd32' => 'Lime', '#4169e1' => 'Royal Blue', '#ffd700' => 'Or'
                                        ];
                                    @endphp
                                    @foreach($popularColors as $hex => $name)
                                        <button type="button"
                                                class="w-8 h-8 rounded-full border-2 border-gray-300 hover:border-blue-500 transition-colors shadow-sm"
                                                style="background-color: {{ $hex }}"
                                                onclick="selectColor('{{ $hex }}', '{{ $name }}')"
                                                title="{{ $name }}">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('couleur')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tailles -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tailles Disponibles *</label>
                        <p class="text-xs text-gray-500 mb-2">Cochez des tailles standards ou ajoutez des tailles personnalisées (ex: 37, 32 Bébé, ESPA 37).</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                            @php
                                $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'];
                            @endphp
                            @foreach($sizes as $size)
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="tailles[]" value="{{ $size }}"
                                           @checked(in_array($size, old('tailles', [])))
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="text" id="customSizeInput" placeholder="Ex: ESPA 37, 32 Bébé" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" onclick="addCustomSize()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Ajouter</button>
                        </div>
                        <div id="customSizesContainer" class="flex flex-wrap gap-2 mt-3"></div>
                        <input type="hidden" id="customSizesHidden" name="tailles[]">
                        @error('tailles')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix Admin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix Admin (MAD) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">MAD</span>
                            <input type="number" name="prix_admin" value="{{ old('prix_admin') }}" step="0.01" min="0" required
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
                            <input type="number" name="prix_vente" value="{{ old('prix_vente') }}" step="0.01" min="0" required
                                   class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        @error('prix_vente')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantité en Stock -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité en Stock *</label>
                        <input type="number" name="quantite_stock" value="{{ old('quantite_stock', 0) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('quantite_stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Image du Produit</label>
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
                        <i class="fas fa-save mr-2"></i>Créer le Produit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Mise à jour automatique du nom de couleur quand la couleur change
document.querySelector('input[name="couleur"]').addEventListener('change', function() {
    const colorNames = {
        '#ff0000': 'Rouge', '#00ff00': 'Vert', '#0000ff': 'Bleu', '#ffff00': 'Jaune',
        '#ff00ff': 'Magenta', '#00ffff': 'Cyan', '#000000': 'Noir', '#ffffff': 'Blanc',
        '#ffa500': 'Orange', '#800080': 'Violet', '#ffc0cb': 'Rose', '#a52a2a': 'Marron',
        '#ff4500': 'Orange-Rouge', '#32cd32': 'Lime', '#4169e1': 'Royal Blue', '#ffd700': 'Or'
    };

    const colorInput = document.querySelector('input[name="couleur_text"]');
    const selectedColor = this.value;

    if (colorNames[selectedColor]) {
        colorInput.value = colorNames[selectedColor];
    }
});

// Fonction pour sélectionner une couleur depuis la palette
function selectColor(hex, name) {
    document.querySelector('input[name="couleur"]').value = hex;
    document.querySelector('input[name="couleur_text"]').value = name;

    // Mise à jour visuelle
    document.querySelector('input[name="couleur"]').dispatchEvent(new Event('change'));
}

// Pré-remplir le nom de couleur au chargement si une couleur est sélectionnée
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.querySelector('input[name="couleur"]');
    const colorTextInput = document.querySelector('input[name="couleur_text"]');

    if (colorInput.value && !colorTextInput.value) {
        colorInput.dispatchEvent(new Event('change'));
    }
});

// Gestion des tailles personnalisées
function addCustomSize() {
    const input = document.getElementById('customSizeInput');
    const container = document.getElementById('customSizesContainer');
    let value = (input.value || '').trim();
    if (!value) return;

    // Normalisation simple (éviter les doublons exacts)
    const exists = Array.from(document.querySelectorAll('input[name="tailles[]"]'))
        .some(el => el.value.toLowerCase() === value.toLowerCase());
    if (exists) {
        input.value = '';
        return;
    }

    // Créer un tag + checkbox masquée
    const id = 'custom-size-' + Date.now();
    const wrapper = document.createElement('span');
    wrapper.className = 'inline-flex items-center bg-blue-50 text-blue-700 px-2 py-1 rounded border border-blue-200';
    wrapper.innerHTML = `<input type="checkbox" name="tailles[]" id="${id}" value="${value}" checked class="hidden"><span class="mr-2">${value}</span><button type="button" class="text-blue-600 hover:text-blue-800" onclick="this.parentElement.remove()">&times;</button>`;
    container.appendChild(wrapper);
    input.value = '';
}
</script>
@endsection


