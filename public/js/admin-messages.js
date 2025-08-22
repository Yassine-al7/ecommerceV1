/**
 * Gestion des Messages Admin - JavaScript Propre
 *
 * Ce fichier gère toutes les fonctionnalités des messages admin
 * sans appels JSON problématiques.
 */

// Variables globales
let selectedMessages = new Set();

// ========================================
// FONCTIONS DE SÉLECTION
// ========================================

/**
 * Mettre à jour la sélection des messages
 */
function updateSelection() {
    try {
        const checkboxes = document.querySelectorAll('.message-checkbox:checked');
        selectedMessages = new Set(Array.from(checkboxes).map(cb => cb.value));

        const selectedCount = document.getElementById('selectedCount');
        const bulkActions = document.getElementById('bulkActions');

        if (selectedCount && bulkActions) {
            selectedCount.textContent = `${selectedMessages.size} sélectionné(s)`;

            if (selectedMessages.size > 0) {
                bulkActions.style.display = 'flex';
            } else {
                bulkActions.style.display = 'none';
            }
        }

        // Mettre à jour l'état de "Sélectionner tout"
        updateSelectAllState();

    } catch (error) {
        console.error('Erreur dans updateSelection:', error);
    }
}

/**
 * Sélectionner/Désélectionner tout
 */
function toggleSelectAll() {
    try {
        const selectAllCheckbox = document.getElementById('selectAll');
        const messageCheckboxes = document.querySelectorAll('.message-checkbox');

        if (selectAllCheckbox && messageCheckboxes.length > 0) {
            messageCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });

            updateSelection();
        }

    } catch (error) {
        console.error('Erreur dans toggleSelectAll:', error);
    }
}

/**
 * Mettre à jour l'état de "Sélectionner tout"
 */
function updateSelectAllState() {
    try {
        const selectAllCheckbox = document.getElementById('selectAll');
        const messageCheckboxes = document.querySelectorAll('.message-checkbox');
        const checkedCount = document.querySelectorAll('.message-checkbox:checked').length;

        if (selectAllCheckbox && messageCheckboxes.length > 0) {
            if (checkedCount === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCount === messageCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
            }
        }

    } catch (error) {
        console.error('Erreur dans updateSelectAllState:', error);
    }
}

/**
 * Annuler la sélection
 */
function clearSelection() {
    try {
        const messageCheckboxes = document.querySelectorAll('.message-checkbox');
        messageCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        selectedMessages.clear();
        updateSelection();

    } catch (error) {
        console.error('Erreur dans clearSelection:', error);
    }
}

// ========================================
// FONCTIONS INDIVIDUELLES
// ========================================

/**
 * Activer/Désactiver un message individuel
 */
function toggleMessageStatus(messageId, currentStatus) {
    try {
        if (!confirm(`Voulez-vous ${currentStatus ? 'désactiver' : 'activer'} ce message ?`)) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error('Token CSRF non trouvé');
        }

        const token = csrfToken.getAttribute('content');
        if (!token) {
            throw new Error('Contenu du token CSRF vide');
        }

        // Afficher un indicateur de chargement
        const button = event.target.closest('button');
        if (!button) {
            throw new Error('Bouton non trouvé');
        }

        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin text-xs md:text-sm"></i>';
        button.disabled = true;

        // Effectuer la requête
        fetch(`/admin/messages/${messageId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                '_token': token,
                '_method': 'PATCH'
            })
        })
        .then(response => {
            if (response.ok) {
                // Succès - recharger la page
                window.location.reload();
            } else {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
        })
        .catch(error => {
            console.error('Erreur lors du toggle:', error);
            alert(`Erreur lors de la modification du statut: ${error.message}`);

            // Restaurer le bouton
            button.innerHTML = originalContent;
            button.disabled = false;
        });

    } catch (error) {
        console.error('Erreur dans toggleMessageStatus:', error);
        alert(`Erreur: ${error.message}`);
    }
}

/**
 * Supprimer un message individuel
 */
function deleteMessage(messageId) {
    try {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce message ? Cette action est irréversible !')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error('Token CSRF non trouvé');
        }

        const token = csrfToken.getAttribute('content');
        if (!token) {
            throw new Error('Contenu du token CSRF vide');
        }

        // Afficher un indicateur de chargement
        const button = event.target.closest('button');
        if (!button) {
            throw new Error('Bouton non trouvé');
        }

        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin text-xs md:text-sm"></i>';
        button.disabled = true;

        // Effectuer la requête
        fetch(`/admin/messages/${messageId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                '_token': token,
                '_method': 'DELETE'
            })
        })
        .then(response => {
            if (response.ok) {
                // Succès - recharger la page
                window.location.reload();
            } else {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error);
            alert(`Erreur lors de la suppression: ${error.message}`);

            // Restaurer le bouton
            button.innerHTML = originalContent;
            button.disabled = false;
        });

    } catch (error) {
        console.error('Erreur dans deleteMessage:', error);
        alert(`Erreur: ${error.message}`);
    }
}

// ========================================
// FONCTIONS EN LOT
// ========================================

/**
 * Actions en lot - Activer/Désactiver
 */
function bulkToggleStatus() {
    try {
        if (selectedMessages.size === 0) {
            alert('Aucun message sélectionné');
            return;
        }

        if (!confirm(`Voulez-vous modifier le statut de ${selectedMessages.size} message(s) ?`)) {
            return;
        }

        // Vérifier le token CSRF
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) {
            throw new Error('Token CSRF non trouvé dans la page');
        }

        const csrfToken = csrfMeta.getAttribute('content');
        if (!csrfToken) {
            throw new Error('Contenu du token CSRF vide');
        }

        // Afficher un indicateur de chargement
        const button = event.target;
        if (!button) {
            throw new Error('Bouton non trouvé');
        }

        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement...';
        button.disabled = true;

        // Traiter chaque message séquentiellement
        let processed = 0;
        const total = selectedMessages.size;

        const processNext = () => {
            if (processed >= total) {
                // Tous les messages traités
                window.location.reload();
                return;
            }

            const messageId = Array.from(selectedMessages)[processed];
            processed++;

            // Mettre à jour le bouton
            button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Traitement... (${processed}/${total})`;

            fetch(`/admin/messages/${messageId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    '_token': csrfToken,
                    '_method': 'PATCH'
                })
            })
            .then(response => {
                if (response.ok) {
                    // Succès, traiter le suivant
                    processNext();
                } else {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
            })
            .catch(error => {
                console.error(`Erreur pour le message ${messageId}:`, error);
                // Continuer avec le suivant malgré l'erreur
                processNext();
            });
        };

        // Commencer le traitement
        processNext();

    } catch (error) {
        console.error('Erreur dans bulkToggleStatus:', error);
        alert(`Erreur: ${error.message}`);

        // Restaurer le bouton
        if (event.target) {
            event.target.innerHTML = originalContent;
            event.target.disabled = false;
        }
    }
}

/**
 * Actions en lot - Supprimer
 */
function bulkDelete() {
    try {
        if (selectedMessages.size === 0) {
            alert('Aucun message sélectionné');
            return;
        }

        if (!confirm(`Êtes-vous sûr de vouloir supprimer ${selectedMessages.size} message(s) ? Cette action est irréversible !`)) {
            return;
        }

        // Vérifier le token CSRF
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) {
            throw new Error('Token CSRF non trouvé dans la page');
        }

        const csrfToken = csrfMeta.getAttribute('content');
        if (!csrfToken) {
            throw new Error('Contenu du token CSRF vide');
        }

        // Afficher un indicateur de chargement
        const button = event.target;
        if (!button) {
            throw new Error('Bouton non trouvé');
        }

        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Suppression...';
        button.disabled = true;

        // Traiter chaque message séquentiellement
        let processed = 0;
        const total = selectedMessages.size;

        const processNext = () => {
            if (processed >= total) {
                // Tous les messages traités
                window.location.reload();
                return;
            }

            const messageId = Array.from(selectedMessages)[processed];
            processed++;

            // Mettre à jour le bouton
            button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Suppression... (${processed}/${total})`;

            fetch(`/admin/messages/${messageId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    '_token': csrfToken,
                    '_method': 'DELETE'
                })
            })
            .then(response => {
                if (response.ok) {
                    // Succès, traiter le suivant
                    processNext();
                } else {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
            })
            .catch(error => {
                console.error(`Erreur pour le message ${messageId}:`, error);
                // Continuer avec le suivant malgré l'erreur
                processNext();
            });
        };

        // Commencer le traitement
        processNext();

    } catch (error) {
        console.error('Erreur dans bulkDelete:', error);
        alert(`Erreur: ${error.message}`);

        // Restaurer le bouton
        if (event.target) {
            event.target.innerHTML = originalContent;
            event.target.disabled = false;
        }
    }
}

// ========================================
// INITIALISATION
// ========================================

/**
 * Initialisation de la page
 */
function initializeAdminMessages() {
    try {
        console.log('Initialisation des messages admin...');

        // Vérifier que le token CSRF est présent
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) {
            console.error('Token CSRF non trouvé - Vérifiez que la balise meta est présente dans le layout');
        } else if (!csrfMeta.getAttribute('content')) {
            console.error('Contenu du token CSRF vide');
        } else {
            console.log('Token CSRF trouvé et valide');
        }

        // Initialiser la sélection
        updateSelection();

        console.log('Initialisation terminée avec succès');

    } catch (error) {
        console.error('Erreur lors de l\'initialisation:', error);
    }
}

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', initializeAdminMessages);

// Exporter les fonctions pour utilisation globale (si nécessaire)
window.adminMessages = {
    updateSelection,
    toggleSelectAll,
    updateSelectAllState,
    clearSelection,
    toggleMessageStatus,
    deleteMessage,
    bulkToggleStatus,
    bulkDelete,
    initializeAdminMessages
};
