/**
 * Aksara Centralized Media & File Uploader JavaScript Library (Vanilla JS)
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 */

(function (root, factory) {
  if (typeof module === 'object' && module.exports) {
    module.exports = factory();
  } else {
    root.AksaraUploader = factory();
  }
})(typeof window !== 'undefined' ? window : this, function () {
  'use strict';

  /**
   * Centralized Media & File Uploader Class
   */
  class AksaraUploader {
    /**
     * @param {Object} options Configuration options
     */
    constructor(options = {}) {
      this.options = Object.assign(
        {
          endpoint: '/',
          path: '',
          token: '',
          target: null,
          view: 'grid',
          limit: 12,
          page: 1,
          sort: 'newest',
          query: '',
          onSelect: null,
          onUpload: null,
          onDelete: null
        },
        options
      );

      this.init();
    }

    /**
     * Initialize uploader resources and events
     */
    init() {
      this.loadCSS();
      this.setupEvents();
      this.loadMedia();
    }

    /**
     * Inject stylesheet dynamically if not present
     */
    loadCSS() {
      if (!document.getElementById('aksara-uploader-css')) {
        const link = document.createElement('link');
        link.id = 'aksara-uploader-css';
        link.rel = 'stylesheet';
        link.href = (typeof config !== 'undefined' && config.baseUrl ? config.baseUrl : '/') + 'modules/XHR/assets/css/uploader.min.css';
        document.head.appendChild(link);
      }
    }

    /**
     * Fetch media files listing from server
     */
    loadMedia() {
      const container = document.getElementById('uploader-media-container');
      if (!container) return;

      const loadingText = typeof phrase === 'function' ? phrase('Loading media...') : 'Loading media...';
      container.innerHTML = `
        <div class="text-center py-5 text-muted">
          <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
          <p class="mb-0 small">${loadingText}</p>
        </div>
      `;

      const formData = new FormData();
      formData.append('mode', 'fetch');
      formData.append('path', this.options.path);
      formData.append('q', this.options.query);
      formData.append('sort', this.options.sort);
      formData.append('page', this.options.page);
      formData.append('limit', this.options.limit);

      fetch(this.options.endpoint, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then((res) => res.json())
        .then((res) => {
          this.renderMedia(res);
        })
        .catch(() => {
          const errorText = typeof phrase === 'function' ? phrase('Error loading media.') : 'Error loading media.';
          container.innerHTML = `
            <div class="text-center py-5 text-danger">
              <i class="mdi mdi-alert-circle mdi-2x mb-2"></i>
              <p class="mb-0">${errorText}</p>
            </div>
          `;
        });
    }

    /**
     * Render media items in grid or list view
     */
    renderMedia(res) {
      const container = document.getElementById('uploader-media-container');
      if (!container) return;

      const items = res.images || res.files || [];

      if (this.options.view === 'list') {
        container.classList.remove('uploader-grid-view');
        container.classList.add('uploader-list-view');
      } else {
        container.classList.remove('uploader-list-view');
        container.classList.add('uploader-grid-view');
      }

      if (!items.length) {
        const noFilesText = typeof phrase === 'function' ? phrase('No files found') : 'No files found';
        container.innerHTML = `
          <div class="text-center py-5 text-muted">
            <i class="mdi mdi-folder-open-outline mdi-3x mb-2"></i>
            <p class="mb-0">${noFilesText}</p>
          </div>
        `;
      } else {
        container.innerHTML = items.map((item) => this.renderItem(item)).join('');
      }

      this.renderPagination(res);
    }

    /**
     * Render single media item HTML
     */
    renderItem(item) {
      const isImage = item.is_image || /\.(jpg|jpeg|png|gif|webp|svg)$/i.test(item.name);
      const nameAttr = escapeHtml(item.name);
      const urlAttr = escapeHtml(item.url);
      const thumbAttr = escapeHtml(item.thumb || item.url);
      const deleteText = typeof phrase === 'function' ? phrase('Delete') : 'Delete';

      const mediaPreview = isImage && item.url ? `<img src="${thumbAttr}" alt="${nameAttr}" loading="lazy" />` : '<div class="uploader-item-icon"><i class="mdi mdi-file-document-outline"></i></div>';

      if (this.options.view === 'list') {
        const metaText = `${escapeHtml(item.formatted_size || '')} • ${escapeHtml(item.formatted_time || '')}`;
        return `
          <div class="uploader-item" data-name="${nameAttr}" data-url="${urlAttr}">
            ${mediaPreview}
            <div class="uploader-item-info">
              <div class="uploader-item-name">${nameAttr}</div>
              <div class="uploader-item-meta">${metaText}</div>
            </div>
            <button type="button" class="uploader-item-del" data-name="${nameAttr}" title="${deleteText}">
              <i class="mdi mdi-delete"></i>
            </button>
          </div>
        `;
      }

      return `
        <div class="uploader-item" data-name="${nameAttr}" data-url="${urlAttr}">
          ${mediaPreview}
          <div class="uploader-item-name">${nameAttr}</div>
          <button type="button" class="uploader-item-del" data-name="${nameAttr}" title="${deleteText}">
            <i class="mdi mdi-delete"></i>
          </button>
        </div>
      `;
    }

    /**
     * Render pagination links and info count
     */
    renderPagination(res) {
      const page = res.page || 1;
      const totalPages = res.total_pages || 1;
      const total = res.total || 0;

      const infoEl = document.getElementById('uploader-pagination-info');
      if (infoEl) {
        const totalText = typeof phrase === 'function' ? phrase('Total') : 'Total';
        const filesText = typeof phrase === 'function' ? phrase('files') : 'files';
        infoEl.innerHTML = `${totalText}: ${total} ${filesText}`;
      }

      const pagEl = document.getElementById('uploader-pagination');
      if (!pagEl) return;

      if (totalPages <= 1) {
        pagEl.innerHTML = `
          <li class="page-item disabled"><a class="page-link uploader-page-link" href="#" data-page="0">&laquo;</a></li>
          <li class="page-item active"><a class="page-link uploader-page-link" href="#" data-page="1">1</a></li>
          <li class="page-item disabled"><a class="page-link uploader-page-link" href="#" data-page="2">&raquo;</a></li>
        `;
        return;
      }

      let paginationHtml = '';
      const prevDisabled = page <= 1 ? 'disabled' : '';
      paginationHtml += `<li class="page-item ${prevDisabled}"><a class="page-link uploader-page-link" href="#" data-page="${page - 1}">&laquo;</a></li>`;

      for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= page - 2 && i <= page + 2)) {
          const active = i === page ? 'active' : '';
          paginationHtml += `<li class="page-item ${active}"><a class="page-link uploader-page-link" href="#" data-page="${i}">${i}</a></li>`;
        } else if (i === page - 3 || i === page + 3) {
          paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
      }

      const nextDisabled = page >= totalPages ? 'disabled' : '';
      paginationHtml += `<li class="page-item ${nextDisabled}"><a class="page-link uploader-page-link" href="#" data-page="${page + 1}">&raquo;</a></li>`;

      pagEl.innerHTML = paginationHtml;
    }

    /**
     * Display error message banner below dropzone
     */
    showError(message) {
      this.clearError();
      const dropZone = document.getElementById('uploader-dropzone');
      if (!dropZone) return;

      let errorMsg = typeof phrase === 'function' ? phrase('Failed to upload file.') : 'Failed to upload file.';
      if (typeof message === 'string') {
        errorMsg = message;
      } else if (message && typeof message === 'object') {
        if (message.file) {
          errorMsg = message.file;
        } else if (message.message) {
          errorMsg = message.message;
        } else if (message.messages) {
          errorMsg = typeof message.messages === 'object' ? Object.values(message.messages).join(', ') : String(message.messages);
        } else {
          errorMsg = JSON.stringify(message);
        }
      }

      const alertEl = document.createElement('div');
      alertEl.id = 'uploader-error-alert';
      alertEl.className = 'alert alert-danger alert-dismissible my-3 px-3 py-1 small rounded-3';
      alertEl.setAttribute('role', 'alert');

      const iconEl = document.createElement('i');
      iconEl.className = 'mdi mdi-alert-circle-outline me-2';

      const textNode = document.createTextNode(' ' + errorMsg);

      const closeBtn = document.createElement('button');
      closeBtn.type = 'button';
      closeBtn.className = 'btn-close p-2';
      closeBtn.setAttribute('data-bs-dismiss', 'alert');
      closeBtn.setAttribute('aria-label', 'Close');

      alertEl.appendChild(iconEl);
      alertEl.appendChild(textNode);
      alertEl.appendChild(closeBtn);

      dropZone.insertAdjacentElement('afterend', alertEl);
    }

    /**
     * Clear any existing error alert box below dropzone
     */
    clearError() {
      const existingAlert = document.getElementById('uploader-error-alert');
      if (existingAlert) {
        existingAlert.remove();
      }
    }

    /**
     * Show Bootstrap confirmation modal for file deletion
     */
    confirmDelete(filename) {
      const modalId = `uploader-delete-modal-${Date.now()}`;
      const titleText = typeof phrase === 'function' ? phrase('Delete Data') : 'Delete Data';
      const bodyText = typeof phrase === 'function' ? phrase('Are you sure want to delete this data?') : 'Are you sure want to delete this data?';
      const cancelText = typeof phrase === 'function' ? phrase('Cancel') : 'Cancel';
      const deleteText = typeof phrase === 'function' ? phrase('Delete') : 'Delete';

      const modalHtml = `
        <div class="modal modal-alert fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}-title" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered rounded-4" role="document" style="max-width:360px">
            <div class="modal-content border-hover rounded-4 border">
              <div class="modal-body text-center">
                <h5>${titleText}</h5>
                <div>${bodyText}</div>
              </div>
              <div class="modal-footer flex-nowrap p-0">
                <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0 border-end" data-bs-dismiss="modal">${cancelText}</button>
                <button type="button" class="btn btn-lg btn-link text-danger fs-6 text-decoration-none col-6 m-0 rounded-0 uploader-delete-confirm">
                  <i class="mdi mdi-check"></i> ${deleteText}
                </button>
              </div>
            </div>
          </div>
        </div>
      `;

      document.body.insertAdjacentHTML('beforeend', modalHtml);
      const modalEl = document.getElementById(modalId);

      let modalInst = null;
      if (window.bootstrap && bootstrap.Modal) {
        modalInst = new bootstrap.Modal(modalEl);
        modalInst.show();
      } else if (typeof $ !== 'undefined' && $.fn.modal) {
        $(modalEl).modal('show');
      }

      const confirmBtn = modalEl.querySelector('.uploader-delete-confirm');
      confirmBtn.onclick = () => {
        if (modalInst) {
          modalInst.hide();
        } else if (typeof $ !== 'undefined' && $.fn.modal) {
          $(modalEl).modal('hide');
        }
        modalEl.remove();
        this.deleteFile(filename);
      };

      modalEl.addEventListener('hidden.bs.modal', () => {
        modalEl.remove();
      });
    }

    /**
     * Bind event listeners for search, sort, file upload, and selection
     */
    setupEvents() {
      const self = this;

      // File input change
      const fileInput = document.getElementById('uploader-file-input');
      if (fileInput) {
        fileInput.onchange = function () {
          if (this.files && this.files.length) {
            self.uploadFiles(this.files);
          }
        };
      }

      // Drop zone drag and drop & click handler
      const dropZone = document.querySelector('.uploader-upload-zone');
      if (dropZone) {
        dropZone.onclick = function (e) {
          if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'LABEL' && !e.target.closest('label')) {
            const input = document.getElementById('uploader-file-input');
            if (input) input.click();
          }
        };

        ['dragenter', 'dragover'].forEach((eventName) => {
          dropZone.addEventListener(
            eventName,
            (e) => {
              e.preventDefault();
              e.stopPropagation();
              dropZone.classList.add('dragover');
            },
            false
          );
        });

        ['dragleave', 'drop'].forEach((eventName) => {
          dropZone.addEventListener(
            eventName,
            (e) => {
              e.preventDefault();
              e.stopPropagation();
              dropZone.classList.remove('dragover');
            },
            false
          );
        });

        dropZone.addEventListener(
          'drop',
          (e) => {
            const dt = e.dataTransfer;
            const files = dt ? dt.files : null;
            if (files && files.length) {
              self.uploadFiles(files);
            }
          },
          false
        );
      }

      // Search input
      const searchInput = document.getElementById('uploader-search-input');
      if (searchInput) {
        searchInput.onkeyup = function () {
          self.options.query = this.value;
          self.options.page = 1;
          self.loadMedia();
        };
      }

      // Sort select
      const sortSelect = document.getElementById('uploader-sort-select');
      if (sortSelect) {
        sortSelect.onchange = function () {
          self.options.sort = this.value;
          self.options.page = 1;
          self.loadMedia();
        };
      }

      // Delegation on document for dynamic elements
      if (!document._aksaraUploaderDelegated) {
        document._aksaraUploaderDelegated = true;

        document.addEventListener('click', (e) => {
          // View toggle (Grid / List)
          const viewBtn = e.target.closest('.uploader-view-toggle');
          if (viewBtn) {
            e.preventDefault();
            document.querySelectorAll('.uploader-view-toggle').forEach((btn) => {
              btn.classList.remove('active');
            });
            viewBtn.classList.add('active');
            self.options.view = viewBtn.getAttribute('data-view');
            self.loadMedia();
            return;
          }

          // Pagination link
          const pageLink = e.target.closest('.uploader-page-link');
          if (pageLink) {
            e.preventDefault();
            const p = pageLink.getAttribute('data-page');
            if (p) {
              self.options.page = parseInt(p, 10);
              self.loadMedia();
            }
            return;
          }

          // Delete item button
          const delBtn = e.target.closest('.uploader-item-del');
          if (delBtn) {
            e.preventDefault();
            e.stopPropagation();
            const filename = delBtn.getAttribute('data-name');
            self.confirmDelete(filename);
            return;
          }

          // Select item
          const itemEl = e.target.closest('#uploader-media-container .uploader-item');
          if (itemEl) {
            const name = itemEl.getAttribute('data-name');
            const url = itemEl.getAttribute('data-url');
            const itemData = { name: name, url: url };

            let targetEl = self.options.target || window._activeUploaderTarget;
            if (typeof targetEl === 'string') {
              targetEl = document.querySelector(targetEl);
            }

            if (targetEl) {
              targetEl.value = url;
              targetEl.dispatchEvent(new Event('input', { bubbles: true }));
              targetEl.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (typeof self.options.onSelect === 'function') {
              self.options.onSelect(itemData);
            }

            if (typeof window._activeUploaderCallback === 'function') {
              window._activeUploaderCallback(itemData);
              window._activeUploaderCallback = null;
            }

            // Close modal
            const modalEl = itemEl.closest('.modal');
            if (modalEl) {
              if (typeof $.fn.modal === 'function') {
                $(modalEl).modal('hide');
              } else {
                const closeBtn = modalEl.querySelector('[data-bs-dismiss="modal"], .btn-close');
                if (closeBtn) closeBtn.click();
              }
            }
          }
        });
      }
    }

    /**
     * Upload selected files to server
     */
    uploadFiles(files) {
      const token = this.options.token;
      this.clearError();

      const maxUploadSizeBytes = typeof config !== 'undefined' && config.maxUploadSize ? config.maxUploadSize * 1024 : 0;
      if (maxUploadSizeBytes > 0) {
        for (let i = 0; i < files.length; i++) {
          if (files[i].size > maxUploadSizeBytes) {
            const errorMsg = typeof phrase === 'function' ? phrase('The selected file size exceeds the maximum allocation') : 'The selected file size exceeds the maximum allocation';
            this.showError(errorMsg);
            return;
          }
        }
      }

      const container = document.getElementById('uploader-media-container');

      if (container) {
        const uploadingText = typeof phrase === 'function' ? phrase('Uploading file...') : 'Uploading file...';
        container.innerHTML = `
          <div class="text-center py-5 text-muted">
            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
            <p class="mb-0 small">${uploadingText}</p>
          </div>
        `;
      }

      const formData = new FormData();
      formData.append('path', this.options.path);
      if (token) formData.append('_token', token);

      for (let i = 0; i < files.length; i++) {
        formData.append('image', files[i]);
        formData.append('file', files[i]);
      }

      let uploadUrl = this.options.endpoint;
      if (!uploadUrl.endsWith('/upload')) {
        uploadUrl = uploadUrl.replace(/\/$/, '') + '/upload';
      }

      fetch(uploadUrl, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(async (res) => {
          const data = await res.json().catch(() => ({}));
          if (!res.ok || data.status === 400 || data.status === 403 || data.status === 422 || data.error) {
            const msg = data.messages || data.message || data.error || (typeof phrase === 'function' ? phrase('Failed to upload file.') : 'Failed to upload file.');
            this.showError(msg);
          } else {
            if (typeof this.options.onUpload === 'function') {
              this.options.onUpload(data);
            }
          }
          this.loadMedia();
        })
        .catch((err) => {
          const msg = err && err.message ? err.message : typeof phrase === 'function' ? phrase('Failed to upload file.') : 'Failed to upload file.';
          this.showError(msg);
          this.loadMedia();
        });
    }

    /**
     * Delete selected file from server
     */
    deleteFile(filename) {
      const token = this.options.token;
      this.clearError();

      let deleteUrl = this.options.endpoint;
      if (!deleteUrl.endsWith('/delete')) {
        deleteUrl = deleteUrl.replace(/\/$/, '') + '/delete';
      }

      const container = document.getElementById('uploader-media-container');

      // Show spinner indicator on item being deleted
      if (container) {
        const itemEls = container.querySelectorAll('.uploader-item');
        itemEls.forEach((itemEl) => {
          if (itemEl.getAttribute('data-name') === filename) {
            itemEl.style.opacity = '0.5';
            const delBtn = itemEl.querySelector('.uploader-item-del');
            if (delBtn) {
              delBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
              delBtn.disabled = true;
            }
          }
        });
      }

      const formData = new FormData();
      formData.append('source', filename);
      formData.append('path', this.options.path);
      if (token) formData.append('_token', token);

      fetch(deleteUrl, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(async (res) => {
          const data = await res.json().catch(() => ({}));
          if (!res.ok || data.status === 400 || data.status === 403 || data.status === 422 || data.error) {
            const msg = data.messages || data.message || data.error || (typeof phrase === 'function' ? phrase('Failed to delete file.') : 'Failed to delete file.');
            this.showError(msg);
          } else {
            if (typeof this.options.onDelete === 'function') {
              this.options.onDelete(filename);
            }
          }
          this.loadMedia();
        })
        .catch((err) => {
          const msg = err && err.message ? err.message : typeof phrase === 'function' ? phrase('Failed to delete file.') : 'Failed to delete file.';
          this.showError(msg);
          this.loadMedia();
        });
    }
  }

  /**
   * Helper function to escape HTML special characters
   */
  function escapeHtml(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  return AksaraUploader;
});
