@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
             <h1 class="h3 mb-0 text-gray-800">Ajouter un nouveau produit</h1>
        </div>
    </div>

    <!-- CLEAN FORM IMPLEMENTATION -->
    <form action="{{ route('admins.products.store') }}" method="POST" enctype="multipart/form-data" id="finalProductForm">
        @csrf
        
        <div class="row">
            <!-- Left Column: Basic Info -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informations de base</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Name -->
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Ex: Robe d'été fleurie">
                        </div>

                        <!-- Description -->
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="5" required placeholder="Description détaillée du produit..."></textarea>
                            <small class="text-muted">Évitez les caractères spéciaux complexes si possible.</small>
                        </div>

                        <!-- Category -->
                        <div class="form-group mb-3">
                            <label for="categorie_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="categorie_id" name="categorie_id" required onchange="toggleSizeSection()">
                                <option value="" disabled selected>Choisir une catégorie...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <!-- Colors & Sizes Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Couleurs et Tailles</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Color Picker UI -->
                        <div class="mb-4">
                            <label class="form-label d-block">Couleurs disponibles</label>
                            <div class="d-flex flex-wrap gap-2" id="colorPalette">
                                <!-- Predefined Colors -->
                                @php
                                    $basicColors = [
                                        ['name' => 'Rouge', 'hex' => '#FF0000'],
                                        ['name' => 'Bleu', 'hex' => '#0000FF'],
                                        ['name' => 'Vert', 'hex' => '#008000'],
                                        ['name' => 'Jaune', 'hex' => '#FFFF00'],
                                        ['name' => 'Noir', 'hex' => '#000000'],
                                        ['name' => 'Blanc', 'hex' => '#FFFFFF'],
                                        ['name' => 'Rose', 'hex' => '#FFC0CB'],
                                        ['name' => 'Gris', 'hex' => '#808080'],
                                        ['name' => 'Beige', 'hex' => '#F5F5DC'],
                                        ['name' => 'Marron', 'hex' => '#A52A2A'],
                                        ['name' => 'Orange', 'hex' => '#FFA500'],
                                        ['name' => 'Violet', 'hex' => '#800080'],
                                    ];
                                @endphp
                                @foreach($basicColors as $color)
                                    <div class="color-item" data-name="{{ $color['name'] }}" data-hex="{{ $color['hex'] }}" onclick="toggleColor(this)">
                                        <div class="color-circle" style="background-color: {{ $color['hex'] }}; border: 1px solid #ddd;"></div>
                                        <span class="color-name">{{ $color['name'] }}</span>
                                        <input type="number" class="form-control form-control-sm stock-input d-none" placeholder="Stock" min="0" value="0">
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Custom Color Input -->
                            <div class="mt-3 row">
                                <div class="col-md-4">
                                    <input type="text" id="customColorName" class="form-control" placeholder="Nom couleur (ex: Turquoise)">
                                </div>
                                <div class="col-md-2">
                                    <input type="color" id="customColorHex" class="form-control form-control-color w-100" value="#563d7c">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-secondary w-100" onclick="addCustomColor()">Ajouter</button>
                                </div>
                            </div>
                        </div>

                        <!-- Sizes UI -->
                        <div id="sizesSection">
                            <label class="form-label d-block">Tailles disponibles</label>
                            <div class="btn-group" role="group">
                                @foreach(['XS','S','M','L','XL','XXL','3XL'] as $size)
                                    <input type="checkbox" class="btn-check size-option" id="size_{{$size}}" value="{{$size}}" autocomplete="off">
                                    <label class="btn btn-outline-secondary" for="size_{{$size}}">{{$size}}</label>
                                @endforeach
                            </div>
                        </div>

                        <!-- HIDDEN INPUTS FOR VARIANTS (JSON payload) -->
                        <input type="hidden" name="colors_json" id="colors_json">
                        <input type="hidden" name="sizes_json" id="sizes_json">
                        <input type="hidden" name="total_stock" id="total_stock">

                    </div>
                </div>
            </div>

            <!-- Right Column: Pricing & Image -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tarification & Image</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Admin Price -->
                        <div class="form-group mb-3">
                            <label for="prix_admin" class="form-label">Prix d'achat (Admin) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prix_admin" name="prix_admin" required 
                                   placeholder="Ex: 100, 120 (Prix multiples)">
                            <small class="text-muted">Séparez par virgules pour plusieurs prix.</small>
                        </div>

                        <!-- Selling Price -->
                        <div class="form-group mb-3">
                            <label for="prix_vente" class="form-label">Prix de vente <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="prix_vente" name="prix_vente" required>
                        </div>

                        <!-- Image Upload -->
                        <div class="form-group mb-3">
                            <label class="form-label">Image Principale</label>
                            <input type="file" class="form-control" id="imageInput" name="image" accept="image/*" onchange="previewImage(this)">
                            <div id="imagePreview" class="mt-2 text-center" style="display:none;">
                                <img src="" alt="Aperçu" style="max-height: 200px; max-width: 100%; border-radius: 5px;">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Submit Card -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-save"></i> Enregistrer le produit
                            </button>
                        </div>
                        <div class="mt-3 text-center">
                            <strong>Stock Total: <span id="totalStockDisplay">0</span></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .color-item {
        cursor: pointer;
        padding: 5px;
        border-radius: 8px;
        border: 2px solid transparent;
        text-align: center;
        width: 80px;
        transition: all 0.2s;
    }
    .color-item.selected {
        border-color: #4e73df;
        background-color: #f8f9fc;
    }
    .color-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin: 0 auto 5px;
    }
    .color-name {
        display: block;
        font-size: 0.8rem;
        margin-bottom: 5px;
    }
    .stock-input {
        width: 100%;
        text-align: center;
    }
</style>

<script>
    // Toggle Size Section based on category
    function toggleSizeSection() {
        const select = document.getElementById('categorie_id');
        const text = select.options[select.selectedIndex].text.toLowerCase();
        const sizeSec = document.getElementById('sizesSection');
        if (text.includes('accessoire')) {
            sizeSec.style.display = 'none';
        } else {
            sizeSec.style.display = 'block';
        }
    }

    // Toggle Selection of Color
    function toggleColor(element) {
        element.classList.toggle('selected');
        const input = element.querySelector('.stock-input');
        if (element.classList.contains('selected')) {
            input.classList.remove('d-none');
            input.value = 1; // Default stock
            input.focus();
        } else {
            input.classList.add('d-none');
            input.value = 0;
        }
        updateTotalStock();
    }

    // Add Custom Color
    function addCustomColor() {
        const name = document.getElementById('customColorName').value.trim();
        const hex = document.getElementById('customColorHex').value;
        if (!name) return alert('Nom de couleur requis');

        const palette = document.getElementById('colorPalette');
        const div = document.createElement('div');
        div.className = 'color-item selected'; // Auto select
        div.setAttribute('data-name', name);
        div.setAttribute('data-hex', hex);
        div.onclick = function() { toggleColor(this); };
        div.innerHTML = `
            <div class="color-circle" style="background-color: ${hex}; border: 1px solid #ddd;"></div>
            <span class="color-name">${name}</span>
            <input type="number" class="form-control form-control-sm stock-input" placeholder="Stock" min="0" value="1">
        `;
        palette.appendChild(div);
        
        // Reset inputs
        document.getElementById('customColorName').value = '';
        updateTotalStock();
    }

    // Update Total Stock Display
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('stock-input')) {
            updateTotalStock();
        }
    });

    function updateTotalStock() {
        let total = 0;
        document.querySelectorAll('.color-item.selected .stock-input').forEach(inp => {
            total += parseInt(inp.value) || 0;
        });
        document.getElementById('totalStockDisplay').innerText = total;
        document.getElementById('total_stock').value = total;
    }

    // Image Preview
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.querySelector('#imagePreview img');
                img.src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Form Submission Handler
    document.getElementById('finalProductForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // 1. Gather Data
        const colors = [];
        document.querySelectorAll('.color-item.selected').forEach(el => {
            colors.push({
                name: el.getAttribute('data-name'),
                hex: el.getAttribute('data-hex'),
                stock: parseInt(el.querySelector('.stock-input').value) || 0
            });
        });

        const sizes = [];
        // Only gather sizes if section is visible
        if (document.getElementById('sizesSection').style.display !== 'none') {
            document.querySelectorAll('.size-option:checked').forEach(el => sizes.push(el.value));
            if (sizes.length === 0) { alert('Veuillez sélectionner au moins une taille.'); return; }
        }

        if (colors.length === 0) { alert('Veuillez sélectionner au moins une couleur.'); return; }

        // 2. Populate Hidden JSON Inputs
        document.getElementById('colors_json').value = JSON.stringify(colors);
        document.getElementById('sizes_json').value = JSON.stringify(sizes);

        // 3. Submit normally
        this.submit();
    });
</script>
@endsection
