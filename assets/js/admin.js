/**
 * FUNKTOGRAPHER - Admin Backoffice JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. File Upload Preview & Dynamic Dropzone Feedback
  const fileInputs = document.querySelectorAll('input[type="file"]');
  fileInputs.forEach(input => {
    const previewId = input.getAttribute('data-preview');
    const infoId = input.getAttribute('data-info');
    const previewImg = previewId ? document.getElementById(previewId) : null;
    const infoElem = infoId ? document.getElementById(infoId) : null;
    const dropzone = input.closest('.upload-dropzone');

    // Drag and Drop Visual Highlight
    if (dropzone) {
      input.addEventListener('dragenter', () => {
        dropzone.style.borderColor = 'var(--adm-primary)';
        dropzone.style.background = 'rgba(255, 197, 1, 0.08)';
      });
      input.addEventListener('dragleave', () => {
        dropzone.style.borderColor = 'var(--adm-border)';
        dropzone.style.background = 'rgba(255, 255, 255, 0.02)';
      });
      input.addEventListener('drop', () => {
        dropzone.style.borderColor = 'var(--adm-border)';
        dropzone.style.background = 'rgba(255, 255, 255, 0.02)';
      });
    }

    input.addEventListener('change', () => {
      const file = input.files[0];
      if (file) {
        const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
        if (infoElem) {
          infoElem.innerHTML = `<strong style="color: var(--adm-primary);">✓ Archivo seleccionado:</strong> ${file.name} (${sizeMb} MB)`;
        }
        if (previewImg && file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = (e) => {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
          };
          reader.readAsDataURL(file);
        }
      }
    });
  });

  // 2. Dynamic Video Type Toggle in project-form.php
  const videoTypeSelect = document.getElementById('videoTypeSelect');
  const youtubeGroup = document.getElementById('youtubeInputGroup');
  const uploadVideoGroup = document.getElementById('uploadVideoGroup');

  function updateVideoFields() {
    if (!videoTypeSelect) return;
    const type = videoTypeSelect.value;
    if (youtubeGroup) {
      youtubeGroup.style.display = (type === 'youtube') ? 'block' : 'none';
    }
    if (uploadVideoGroup) {
      uploadVideoGroup.style.display = (type === 'upload') ? 'block' : 'none';
    }
  }

  if (videoTypeSelect) {
    videoTypeSelect.addEventListener('change', updateVideoFields);
    updateVideoFields(); // Initial call
  }

  // 3. YouTube Live Preview
  const ytInput = document.getElementById('youtubeUrlInput');
  const ytPreview = document.getElementById('youtubePreviewBox');
  if (ytInput && ytPreview) {
    function checkYt() {
      const val = ytInput.value.trim();
      const match = val.match(/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:embed\/|v\/|watch\?v=|shorts\/))([\w-]{11})/);
      if (match && match[1]) {
        ytPreview.innerHTML = `
          <div style="margin-top: 14px; border-radius: 8px; overflow: hidden; max-width: 480px; aspect-ratio: 16/9; background: #000; border: 1px solid var(--adm-border);">
            <iframe src="https://www.youtube-nocookie.com/embed/${match[1]}?rel=0" style="width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
          </div>
        `;
      } else {
        ytPreview.innerHTML = '';
      }
    }
    ytInput.addEventListener('input', checkYt);
    checkYt();
  }

  // 4. Auto Slug Generator (in project-form.php)
  const titleInput = document.getElementById('projectTitle');
  const slugInput = document.getElementById('projectSlug');
  if (titleInput && slugInput && slugInput.value === '') {
    titleInput.addEventListener('input', () => {
      const slug = titleInput.value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)+/g, '');
      slugInput.value = slug;
    });
  }

  // 5. Confirm Delete Actions
  const deleteButtons = document.querySelectorAll('[data-confirm]');
  deleteButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const msg = btn.getAttribute('data-confirm') || '¿Estás seguro de realizar esta acción?';
      if (!confirm(msg)) {
        e.preventDefault();
      }
    });
  });
});
