# 🎨 GUIDE: Filtrage Intelligent des Couleurs par Stock

## 📋 Vue d'ensemble

Ce guide explique la nouvelle logique de filtrage des couleurs qui résout le problème des alertes incorrectes et des couleurs manquantes.

## 🚨 Problèmes résolus

### **Avant (problématique) :**
- ❌ Affichage du stock total au lieu du stock par couleur
- ❌ Alertes affichées même pour les produits en stock
- ❌ Couleurs sans stock affichées dans la liste
- ❌ Confusion entre stock total et stock disponible

### **Après (solution) :**
- ✅ Affichage du stock réel par couleur
- ✅ Filtrage automatique des couleurs sans stock
- ✅ Alertes précises basées sur le stock disponible
- ✅ Distinction claire entre stock total et stock par couleur

## 🔧 Logique de filtrage implémentée

### 1. **Filtrage des couleurs lors de la sélection du produit**

```javascript
// Mettre à jour les couleurs en filtrant celles sans stock
const couleurSelect = row.querySelector('.couleur-select');
couleurSelect.innerHTML = '<option value="">Sélectionner une couleur</option>';

// Récupérer les stocks par couleur
let couleursDisponibles = [];
if (selectedOption.getAttribute('data-stock-couleurs')) {
    try {
        const stockCouleurs = JSON.parse(selectedOption.getAttribute('data-stock-couleurs'));
        couleurs.forEach(couleur => {
            const couleurName = typeof couleur === 'string' ? couleur : couleur.name;
            
            // Chercher le stock pour cette couleur
            let stockCouleur = 0;
            stockCouleurs.forEach(stockData => {
                if (stockData.name === couleurName) {
                    stockCouleur = parseInt(stockData.quantity) || 0;
                }
            });
            
            // Ajouter la couleur seulement si elle a du stock
            if (stockCouleur > 0) {
                couleursDisponibles.push({
                    name: couleurName,
                    stock: stockCouleur
                });
            }
        });
    } catch (e) {
        console.error('Erreur parsing stock_couleurs:', e);
    }
}
```

### 2. **Affichage des couleurs avec leur stock**

```javascript
// Afficher les couleurs disponibles avec leur stock
couleursDisponibles.forEach(couleurData => {
    const option = document.createElement('option');
    option.value = couleurData.name;
    option.textContent = `${couleurData.name} (Stock: ${couleurData.stock})`;
    option.setAttribute('data-stock', couleurData.stock);
    couleurSelect.appendChild(option);
});
```

### 3. **Vérification du stock lors de la sélection de couleur**

```javascript
// Récupérer le stock depuis l'option de couleur sélectionnée
const couleurOption = couleurSelect.options[couleurSelect.selectedIndex];
if (couleurOption && couleurOption.getAttribute('data-stock')) {
    const stockFromOption = couleurOption.getAttribute('data-stock');
    if (stockFromOption !== 'N/A') {
        stockCouleur = parseInt(stockFromOption) || 0;
        couleurTrouvee = true;
    }
}
```

## 🎯 Comportement attendu

### **Scénario 1: Produit avec plusieurs couleurs, certaines en rupture**

**Produit:** T-shirt avec couleurs Rouge, Bleu, Vert
**Stock par couleur:**
- Rouge: 15 unités ✅
- Bleu: 0 unités ❌
- Vert: 8 unités ✅

**Résultat:**
- ✅ Rouge (Stock: 15) - affiché
- ❌ Bleu - masqué (stock = 0)
- ✅ Vert (Stock: 8) - affiché

### **Scénario 2: Produit entièrement en rupture**

**Produit:** Chaussures avec couleurs Noir, Blanc
**Stock par couleur:**
- Noir: 0 unités ❌
- Blanc: 0 unités ❌

**Résultat:**
- ❌ Aucune couleur affichée
- ❌ Produit considéré comme indisponible

### **Scénario 3: Produit avec stock suffisant**

**Produit:** Sac avec couleurs Marron, Beige
**Stock par couleur:**
- Marron: 25 unités ✅
- Beige: 18 unités ✅

**Résultat:**
- ✅ Marron (Stock: 25) - affiché
- ✅ Beige (Stock: 18) - affiché

## 🚨 Logique des alertes

### **Alertes affichées:**

1. **🚨 Danger (Rouge):**
   - Couleur en rupture (0 disponible)
   - Couleur non disponible ou sans stock

2. **⚠️ Warning (Jaune):**
   - Stock couleur insuffisant pour la quantité demandée

3. **✅ Success (Vert):**
   - Stock suffisant pour la commande

### **Alertes NON affichées:**
- ❌ Stock total insuffisant (si la couleur a assez de stock)
- ❌ Couleurs masquées (elles n'apparaissent pas dans la liste)

## 🧪 Tests à effectuer

### **1. Test du filtrage des couleurs:**
```bash
php test_filtrage_couleurs.php
```

**Vérifications:**
- ✅ Seules les couleurs avec stock > 0 sont affichées
- ✅ Le stock est affiché à côté de chaque couleur
- ✅ Les couleurs sans stock sont masquées

### **2. Test des alertes:**
- Sélectionnez un produit
- Choisissez une couleur avec stock suffisant
- Vérifiez qu'aucune alerte rouge n'apparaît
- Modifiez la quantité pour dépasser le stock
- Vérifiez que l'alerte jaune apparaît

### **3. Test des produits en rupture:**
- Sélectionnez un produit avec toutes les couleurs en rupture
- Vérifiez qu'aucune couleur n'apparaît
- Vérifiez que le produit est considéré comme indisponible

## 🔍 Débogage

### **Si les couleurs n'apparaissent pas:**

1. **Vérifiez la base de données:**
   ```bash
   php test_stock_couleurs.php
   ```

2. **Vérifiez le format des données:**
   - `stock_couleurs` doit être un JSON valide
   - Chaque couleur doit avoir `name` et `quantity`
   - Les quantités doivent être des nombres

3. **Corrigez les données si nécessaire:**
   ```bash
   php fix_stock_couleurs.php
   ```

### **Si les alertes sont incorrectes:**

1. **Vérifiez la console JavaScript** pour les erreurs
2. **Vérifiez que `data-stock-couleurs`** est bien défini
3. **Vérifiez que les attributs `data-stock`** sont corrects

## 📱 Interface utilisateur

### **Affichage des couleurs:**
```
Rouge (Stock: 15)     ✅ Disponible
Bleu (Stock: 0)       ❌ Masqué
Vert (Stock: 8)       ✅ Disponible
```

### **Affichage du stock:**
```
Stock: 15             🟢 Vert (suffisant)
Stock: 8              🟡 Jaune (attention)
Stock: 0              🔴 Rouge (rupture)
```

### **Affichage des alertes:**
```
🚨 Rouge en rupture (0 disponible)
⚠️ Stock insuffisant (8 < 10)
✅ Stock suffisant (15 disponible)
```

## 🚀 Avantages de cette approche

1. **🎯 Précision:** Alertes basées sur le stock réel de la couleur
2. **👁️ Visibilité:** Seules les couleurs disponibles sont affichées
3. **🚫 Prévention:** Impossible de sélectionner une couleur sans stock
4. **📊 Transparence:** Stock affiché clairement pour chaque couleur
5. **🔄 Réactivité:** Mise à jour en temps réel des alertes

---

**💡 Conseil:** Testez d'abord avec `php test_filtrage_couleurs.php` pour vérifier que le filtrage fonctionne, puis testez dans votre interface utilisateur.
