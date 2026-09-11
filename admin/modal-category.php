<?php
/**
 * Quick Category Management Modal (Create, Edit, Delete, Reorder)
 * Usable inside admin/home-images.php, admin/project-form.php, and admin/post-form.php
 */
$targetSelectId = $categorySelectId ?? 'category';
?>
<!-- Quick Category Management Modal -->
<div class="adm-modal-overlay" id="catModalOverlay">
  <div class="adm-modal-box" style="max-width: 620px; width: 95%;">
    <!-- Header with Tabs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--adm-border);">
      <div style="display: flex; gap: 12px; align-items: center;">
        <button type="button" class="cat-tab-btn active" id="tabBtnNewCat" style="background: none; border: none; font-size: 1rem; font-weight: 700; color: #fff; cursor: pointer; padding: 4px 8px; border-bottom: 2px solid var(--adm-primary);">
          <i class="fas fa-plus-circle" style="color: var(--adm-primary);"></i> Nueva Categoría
        </button>
        <button type="button" class="cat-tab-btn" id="tabBtnListCat" style="background: none; border: none; font-size: 1rem; font-weight: 600; color: var(--adm-muted); cursor: pointer; padding: 4px 8px; border-bottom: 2px solid transparent;">
          <i class="fas fa-edit"></i> Editar / Eliminar (<span id="catCountBadge">0</span>)
        </button>
      </div>
      <button type="button" id="btnCloseCatModal" style="background: none; border: none; color: var(--adm-muted); font-size: 1.3rem; cursor: pointer; line-height: 1;">
        &times;
      </button>
    </div>

    <!-- Alert Message Box -->
    <div id="catModalAlert" style="display: none; padding: 10px 14px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 14px;"></div>

    <!-- TAB 1: CREATE NEW CATEGORY -->
    <div id="tabContentNewCat">
      <form id="quickCatForm">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="create">

        <div class="adm-form-group">
          <label class="adm-form-label">Nombre de la Categoría *</label>
          <input type="text" id="modalCatName" name="name" class="adm-input" placeholder="Ej: Fotografía Aérea y Drones" required>
        </div>

        <div class="adm-form-group">
          <label class="adm-form-label">Orden de Visualización (Pills)</label>
          <input type="number" id="modalCatOrder" name="display_order" class="adm-input" value="1" min="1">
          <small style="color: var(--adm-muted); font-size: 0.78rem; display: block; margin-top: 4px;">
            Define qué lugar ocupará en las píldoras del Home y Portafolio (1 = primera).
          </small>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
          <button type="button" class="btn btn-outline btn-sm" id="btnCancelCatModal">Cerrar</button>
          <button type="submit" id="btnSaveCatModal" class="btn btn-primary btn-sm" style="background: var(--adm-primary); color: #09090c; font-weight: 700;">
            <i class="fas fa-check"></i> Guardar y Seleccionar
          </button>
        </div>
      </form>
    </div>

    <!-- TAB 2: MANAGE / EDIT / DELETE EXISTING CATEGORIES -->
    <div id="tabContentListCat" style="display: none;">
      <p style="font-size: 0.85rem; color: var(--adm-muted); margin-bottom: 14px;">
        Modifica el nombre o el orden numérico directamente y haz clic en <i class="fas fa-save" style="color: var(--adm-primary);"></i> para guardar, o en <i class="fas fa-trash" style="color: #f87171;"></i> para eliminar.
      </p>

      <div style="max-height: 320px; overflow-y: auto; padding-right: 4px;">
        <div id="catItemsContainer" style="display: flex; flex-direction: column; gap: 10px;">
          <!-- Loaded dynamically via AJAX -->
          <div style="text-align: center; padding: 20px; color: var(--adm-muted);">
            <i class="fas fa-spinner fa-spin"></i> Cargando categorías...
          </div>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; margin-top: 18px; border-top: 1px solid var(--adm-border); padding-top: 12px;">
        <button type="button" class="btn btn-outline btn-sm" id="btnCloseCatListModal">Listo</button>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  const CSRF_TOKEN = '<?= csrf_token() ?>';
  const TARGET_SELECT_ID = '<?= $targetSelectId ?>';
  const AJAX_URL = '<?= BASE_URL ?>/admin/ajax-category';

  const overlay = document.getElementById('catModalOverlay');
  const btnOpen = document.getElementById('btnOpenCatModal');
  const btnClose = document.getElementById('btnCloseCatModal');
  const btnCancel = document.getElementById('btnCancelCatModal');
  const btnCloseList = document.getElementById('btnCloseCatListModal');
  
  const tabBtnNew = document.getElementById('tabBtnNewCat');
  const tabBtnList = document.getElementById('tabBtnListCat');
  const tabContentNew = document.getElementById('tabContentNewCat');
  const tabContentList = document.getElementById('tabContentListCat');

  const alertBox = document.getElementById('catModalAlert');
  const quickForm = document.getElementById('quickCatForm');
  const catItemsContainer = document.getElementById('catItemsContainer');
  const catCountBadge = document.getElementById('catCountBadge');
  const targetSelect = document.getElementById(TARGET_SELECT_ID);

  function showAlert(msg, isSuccess = false) {
    alertBox.style.display = 'block';
    alertBox.style.background = isSuccess ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)';
    alertBox.style.border = isSuccess ? '1px solid rgba(16, 185, 129, 0.4)' : '1px solid rgba(239, 68, 68, 0.4)';
    alertBox.style.color = isSuccess ? '#34d399' : '#f87171';
    alertBox.innerHTML = (isSuccess ? '<i class="fas fa-check-circle"></i> ' : '<i class="fas fa-exclamation-triangle"></i> ') + msg;
  }

  function hideAlert() {
    alertBox.style.display = 'none';
  }

  function switchTab(tab) {
    hideAlert();
    if (tab === 'new') {
      tabBtnNew.style.color = '#fff';
      tabBtnNew.style.borderBottom = '2px solid var(--adm-primary)';
      tabBtnList.style.color = 'var(--adm-muted)';
      tabBtnList.style.borderBottom = '2px solid transparent';
      tabContentNew.style.display = 'block';
      tabContentList.style.display = 'none';
      setTimeout(() => document.getElementById('modalCatName')?.focus(), 50);
    } else {
      tabBtnList.style.color = '#fff';
      tabBtnList.style.borderBottom = '2px solid var(--adm-primary)';
      tabBtnNew.style.color = 'var(--adm-muted)';
      tabBtnNew.style.borderBottom = '2px solid transparent';
      tabContentNew.style.display = 'none';
      tabContentList.style.display = 'block';
      loadCategoriesList();
    }
  }

  tabBtnNew?.addEventListener('click', () => switchTab('new'));
  tabBtnList?.addEventListener('click', () => switchTab('list'));

  function openModal() {
    overlay.classList.add('active');
    switchTab('new');
    quickForm.reset();
    document.getElementById('modalCatOrder').value = '1';
    loadCategoriesList();
  }

  function closeModal() {
    overlay.classList.remove('active');
    hideAlert();
  }

  btnOpen?.addEventListener('click', openModal);
  btnClose?.addEventListener('click', closeModal);
  btnCancel?.addEventListener('click', closeModal);
  btnCloseList?.addEventListener('click', closeModal);
  overlay?.addEventListener('click', (e) => {
    if (e.target === overlay) closeModal();
  });

  // Sync dropdown with latest category list
  function updateParentSelect(categories, selectedVal = null) {
    if (!targetSelect) return;
    const currentVal = selectedVal || targetSelect.value;
    targetSelect.innerHTML = '';

    categories.forEach(cat => {
      const opt = document.createElement('option');
      // Some selects use name, some use slug
      const isProjectOrPost = (TARGET_SELECT_ID === 'categorySelect');
      opt.value = isProjectOrPost ? cat.name : cat.slug;
      opt.textContent = cat.name;
      if (opt.value === currentVal || cat.name === currentVal || cat.slug === currentVal) {
        opt.selected = true;
      }
      targetSelect.appendChild(opt);
    });
  }

  // Load categories from server
  async function loadCategoriesList() {
    const formData = new FormData();
    formData.append('csrf_token', CSRF_TOKEN);
    formData.append('action', 'list');

    try {
      const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
      const data = await res.json();
      if (data.success && Array.isArray(data.categories)) {
        renderCategoriesList(data.categories);
        catCountBadge.textContent = data.categories.length;
        updateParentSelect(data.categories);
      }
    } catch (e) {
      console.error('Error al cargar categorías:', e);
    }
  }

  // Render editable list
  function renderCategoriesList(categories) {
    catItemsContainer.innerHTML = '';
    catCountBadge.textContent = categories.length;

    if (categories.length === 0) {
      catItemsContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: var(--adm-muted);">No hay categorías registradas.</div>';
      return;
    }

    categories.forEach(cat => {
      const row = document.createElement('div');
      row.style.cssText = 'display: flex; gap: 8px; align-items: center; background: rgba(255,255,255,0.03); border: 1px solid var(--adm-border); border-radius: 8px; padding: 8px 12px; transition: background 0.15s;';
      row.id = `catRow_${cat.id}`;

      row.innerHTML = `
        <div style="width: 55px; flex-shrink: 0;">
          <input type="number" class="adm-input cat-item-order" value="${cat.display_order}" min="0" style="padding: 6px 8px; font-size: 0.85rem; text-align: center;" title="Orden de píldora">
        </div>
        <div style="flex: 1;">
          <input type="text" class="adm-input cat-item-name" value="${cat.name.replace(/"/g, '&quot;')}" style="padding: 6px 10px; font-size: 0.9rem;" placeholder="Nombre de categoría">
        </div>
        <div style="display: flex; gap: 6px; flex-shrink: 0;">
          <button type="button" class="btn btn-outline btn-sm btn-save-cat-item" style="padding: 6px 10px; font-size: 0.8rem; color: var(--adm-primary); border-color: rgba(255,197,1,0.3);" title="Guardar Cambios">
            <i class="fas fa-save"></i>
          </button>
          <button type="button" class="btn btn-outline btn-sm btn-del-cat-item" style="padding: 6px 10px; font-size: 0.8rem; color: #f87171; border-color: rgba(239,68,68,0.3);" title="Eliminar Categoría">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      `;

      // Save category edits
      const saveBtn = row.querySelector('.btn-save-cat-item');
      const delBtn = row.querySelector('.btn-del-cat-item');
      const nameInput = row.querySelector('.cat-item-name');
      const orderInput = row.querySelector('.cat-item-order');

      saveBtn.addEventListener('click', async () => {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        const fd = new FormData();
        fd.append('csrf_token', CSRF_TOKEN);
        fd.append('action', 'update');
        fd.append('id', cat.id);
        fd.append('name', nameInput.value.trim());
        fd.append('display_order', orderInput.value);

        try {
          const res = await fetch(AJAX_URL, { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) {
            showAlert(`Categoría "${nameInput.value.trim()}" actualizada.`, true);
            updateParentSelect(data.categories);
            saveBtn.innerHTML = '<i class="fas fa-check" style="color: #34d399;"></i>';
            setTimeout(() => { saveBtn.innerHTML = '<i class="fas fa-save"></i>'; saveBtn.disabled = false; }, 1500);
          } else {
            showAlert(data.error || 'Error al actualizar categoría.');
            saveBtn.innerHTML = '<i class="fas fa-save"></i>';
            saveBtn.disabled = false;
          }
        } catch (err) {
          showAlert('Error de conexión.');
          saveBtn.innerHTML = '<i class="fas fa-save"></i>';
          saveBtn.disabled = false;
        }
      });

      // Delete category
      delBtn.addEventListener('click', async () => {
        if (!confirm(`¿Eliminar la categoría "${cat.name}"? Los proyectos o fotos asociados mantendrán su texto histórico.`)) {
          return;
        }

        delBtn.disabled = true;
        delBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        const fd = new FormData();
        fd.append('csrf_token', CSRF_TOKEN);
        fd.append('action', 'delete');
        fd.append('id', cat.id);

        try {
          const res = await fetch(AJAX_URL, { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) {
            showAlert(`Categoría "${cat.name}" eliminada.`, true);
            row.style.transition = 'all 0.25s';
            row.style.opacity = '0';
            row.style.transform = 'scale(0.9)';
            setTimeout(() => row.remove(), 250);
            updateParentSelect(data.categories);
            catCountBadge.textContent = data.categories.length;
          } else {
            showAlert(data.error || 'Error al eliminar categoría.');
            delBtn.disabled = false;
            delBtn.innerHTML = '<i class="fas fa-trash"></i>';
          }
        } catch (err) {
          showAlert('Error de conexión.');
          delBtn.disabled = false;
          delBtn.innerHTML = '<i class="fas fa-trash"></i>';
        }
      });

      catItemsContainer.appendChild(row);
    });
  }

  // Create new category form submission
  quickForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const saveBtn = document.getElementById('btnSaveCatModal');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    const formData = new FormData(quickForm);

    try {
      const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
      const data = await res.json();

      if (data.success && data.category) {
        if (data.categories) {
          updateParentSelect(data.categories, data.category.slug);
        }
        showAlert(`Categoría "${data.category.name}" lista y seleccionada.`, true);
        setTimeout(() => closeModal(), 800);
      } else {
        showAlert(data.error || 'Error al crear la categoría.');
      }
    } catch (err) {
      showAlert('Error de conexión con el servidor.');
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="fas fa-check"></i> Guardar y Seleccionar';
    }
  });

})();
</script>
