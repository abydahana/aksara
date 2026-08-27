/**
 * Aksara Page Builder v2
 * A modern, modular, and immersive page building engine for Aksara CMS.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 */
(function () {
  'use strict';

  /**
   * Main Page Builder Class
   * @param {Object} options Configuration options
   */
  window.AksaraPageBuilder = function (options) {
    this.options = options || {};
    this.container = document.querySelector(options.el || '#page-builder');
    this.inputElement = document.querySelector(options.input || '#page_content');
    this.componentDefinitions = options.components || {};
    this.categories = options.categories || {};

    // Customizable CSS classes / element templates config
    this.classes = Object.assign(
      {
        input: 'form-control form-control-sm',
        select: 'form-control form-control-sm',
        textarea: 'form-control form-control-sm',
        color: 'form-control form-control-color',
        number: 'form-control form-control-sm',
        wysiwyg: 'pb-wysiwyg',
        wysiwygEditor: 'pb-wysiwyg-editor',
        selectRole: 'select'
      },
      options.classes || options.templates || {}
    );

    this.framework = options.framework || 'bootstrap5';

    this.layout = {
      version: '1.0',
      framework: this.framework,
      components: []
    };

    this.selectedId = null;
    this.history = [];
    this.historyIndex = -1;
    this.idCounter = 0;
    this.debounceTimer = null;

    if (this.container) {
      this.initialize();
    }
  };

  const Prototype = AksaraPageBuilder.prototype;

  /**
   * Initialize the Page Builder
   */
  Prototype.initialize = function () {
    this.buildUserInterface();
    this.bindEvents();
    this.loadFromInput();
    this.render();
    this.initTooltips();
  };

  /**
   * Initialize Bootstrap Tooltips
   */
  Prototype.initTooltips = function () {
    if (window.bootstrap && bootstrap.Tooltip) {
      document.querySelectorAll('.tooltip').forEach((el) => el.remove());

      const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltips.forEach((tooltipTriggerEl) => {
        bootstrap.Tooltip.getOrCreateInstance(tooltipTriggerEl);
      });
    }
  };

  /**
   * Generate a unique ID for components
   */
  Prototype.generateUniqueId = function () {
    return `pb_${++this.idCounter}_${Date.now().toString(36)}`;
  };

  /**
   * Build the main UI structure
   */
  Prototype.buildUserInterface = function () {
    this.toolbar = document.querySelector('.pb-toolbar');

    if (!this.container.querySelector('.pb-wrapper')) {
      this.container.innerHTML = `
        <div class="pb-wrapper">
          <div class="pb-sidebar" id="pb-sidebar"></div>
          <div class="pb-canvas-wrapper">
            <div class="pb-canvas" id="pb-canvas"></div>
          </div>
          <div class="pb-properties" id="pb-props">
            <div class="pb-props-empty">
              <i class="mdi mdi-cursor-default-click mdi-3x d-block mb-2"></i>
              ${phrase('Select a component to edit its properties')}
            </div>
          </div>
        </div>
      `;
    }

    this.sidebarElement = this.container.querySelector('#pb-sidebar');
    this.canvasElement = this.container.querySelector('#pb-canvas');
    this.propertiesElement = this.container.querySelector('#pb-props');

    this.buildSidebar();
  };

  /**
   * Build the components sidebar
   */
  Prototype.buildSidebar = function () {
    let html = '';
    const categories = this.categories;
    const components = this.componentDefinitions;

    for (const categoryId in categories) {
      html += `<div class="pb-sidebar-title">${phrase(categories[categoryId])}</div>`;

      for (const componentType in components) {
        if (components[componentType].category === categoryId) {
          let group = 'pb-content';
          if (componentType === 'section') group = 'pb-sec';
          else if (componentType === 'container') group = 'pb-con';
          else if (componentType === 'row') group = 'pb-row';
          else if (componentType === 'column') group = 'pb-col';

          const icon = components[componentType].icon || 'mdi mdi-square-outline';
          const label = phrase(components[componentType].label || componentType);

          html += `
            <div class="pb-component-item" data-type="${componentType}" data-group="${group}">
              <i class="${icon}"></i>
              <span>${label}</span>
            </div>
          `;
        }
      }
    }

    this.sidebarElement.innerHTML = html;
  };

  /**
   * Bind all global events
   */
  Prototype.bindEvents = function () {
    const self = this;

    // Sidebar items: make them draggable
    this.sidebarElement.querySelectorAll('.pb-component-item').forEach((item) => {
      item.setAttribute('draggable', 'true');
      item.addEventListener('dragstart', (event) => {
        self.draggedType = item.dataset.type;
        event.dataTransfer.setData('text/plain', item.dataset.type);
        event.dataTransfer.effectAllowed = 'copy';
      });
      item.addEventListener('dragend', () => {
        self.draggedType = null;
      });
    });

    // Canvas clicks and interactions
    this.canvasElement.addEventListener('click', (event) => {
      const actionButton = event.target.closest('[data-act]');

      if (actionButton) {
        const block = actionButton.closest('.pb-block');
        if (!block) return;

        const id = block.dataset.id;
        const action = actionButton.dataset.act;

        event.stopPropagation();

        if (action === 'del') self.deleteComponent(id);
        else if (action === 'dup') self.duplicateComponent(id);
        else if (action === 'up') self.moveComponent(id, -1);
        else if (action === 'down') self.moveComponent(id, 1);

        return;
      }

      const block = event.target.closest('.pb-block');
      if (block) {
        self.selectedId = block.dataset.id;
        self.renderProperties();
        self.highlightComponent();
        event.stopPropagation();
      } else {
        self.selectedId = null;
        self.renderProperties();
        self.highlightComponent();
      }
    });

    // Toolbar interactions
    if (this.toolbar) {
      const undoBtn = this.toolbar.querySelector('.pb-undo');
      if (undoBtn) undoBtn.onclick = () => self.undo();

      const redoBtn = this.toolbar.querySelector('.pb-redo');
      if (redoBtn) redoBtn.onclick = () => self.redo();

      const previewBtn = this.toolbar.querySelector('.pb-preview-btn');
      if (previewBtn) {
        previewBtn.onclick = (event) => {
          event.preventDefault();
          self.preview();
        };
      }

      const saveBtn = this.toolbar.querySelector('.pb-save-btn');
      if (saveBtn) {
        saveBtn.onclick = function () {
          self.submitForm(this);
        };
      }

      this.toolbar.querySelectorAll('.pb-device-btn').forEach((btn) => {
        btn.onclick = () => {
          self.toolbar.querySelectorAll('.pb-device-btn').forEach((x) => x.classList.remove('active'));
          btn.classList.add('active');

          const canvas = self.canvasElement;
          canvas.classList.remove('pb-tablet', 'pb-mobile');

          if (btn.dataset.device === 'tablet') canvas.classList.add('pb-tablet');
          else if (btn.dataset.device === 'mobile') canvas.classList.add('pb-mobile');
        };
      });
    }

    // Delegated click listener for browse image buttons
    document.addEventListener('click', (event) => {
      const browseBtn = event.target.closest('.pb-browse-img');
      if (browseBtn) {
        const input = browseBtn.previousElementSibling;
        if (input && (input.classList.contains('pb-property-input') || input.classList.contains('pb-item-input'))) {
          window._activeUploaderTarget = input;
        }
      }
    });
  };

  /**
   * Create a new component object
   */
  Prototype.createComponent = function (type, overrides, isChild) {
    const definition = this.componentDefinitions[type];
    if (!definition) return null;

    const props = JSON.parse(JSON.stringify(definition.defaults || {}));
    if (overrides) {
      for (const key in overrides) props[key] = overrides[key];
    }

    const component = {
      type: type,
      id: this.generateUniqueId(),
      props: props
    };

    if (definition.children) {
      component.children = [];
      if (type === 'row') {
        component.children = [this.createComponent('column', { size: { md: 12 } }, true)];
      }
    }

    return component;
  };

  Prototype.withQuery = function (url, params) {
    const separator = url.indexOf('?') === -1 ? '?' : '&';
    const search = new URLSearchParams(window.location.search || '');

    if (params) {
      for (const key in params) {
        search.set(key, params[key]);
      }
    }

    const query = search.toString();
    return query ? url + separator + query : url;
  };

  Prototype.wrapComponentForRoot = function (type) {
    if (['section', 'hero', 'carousel'].includes(type)) {
      return this.createComponent(type);
    }

    const section = this.createComponent('section');
    if (!section) return null;

    if (type === 'container') {
      section.children = [this.createComponent('container')];
      return section;
    }

    const container = this.createComponent('container');
    if (!container) return section;

    if (type === 'row') {
      container.children = [this.createComponent('row')];
      section.children = [container];
      return section;
    }

    const row = this.createComponent('row');
    if (!row) {
      section.children = [container];
      return section;
    }

    if (type === 'column') {
      row.children = [this.createComponent('column', { size: { md: 12 } }, true)];
    } else {
      const column = this.createComponent('column', { size: { md: 12 } }, true);
      const content = this.createComponent(type);

      if (column && content) {
        column.children = [content];
        row.children = [column];
      }
    }

    container.children = [row];
    section.children = [container];
    return section;
  };

  /**
   * Render the entire canvas
   */
  Prototype.render = function () {
    let html = '<div class="pb-drop-root" data-type="root">';
    if (this.layout.components.length) {
      html += this.renderBlocks(this.layout.components);
    }
    html += '</div>';

    if (!this.layout.components.length) {
      html += `
        <div class="pb-canvas-empty">
          <i class="mdi mdi-drag-variant"></i>
          <div>${phrase('Drag components here to start building')}</div>
        </div>
      `;
    }
    this.canvasElement.innerHTML = html;

    this.initializeSortable();
    this.initializeColumnResizing();
    this.syncToInput();
    this.highlightComponent();
    this.initTooltips();
  };

  /**
   * Initialize SortableJS on all dropzones
   */
  Prototype.initializeSortable = function () {
    const self = this;
    const zones = this.canvasElement.querySelectorAll('.pb-dropzone');

    zones.forEach((zone) => {
      if (zone._sortable) zone._sortable.destroy();

      zone._sortable = new Sortable(zone, {
        group: {
          name: 'pb-components',
          pull: true,
          put: (to, from, dragged) => {
            const targetEl = to.el;
            const targetType = targetEl.dataset.type || targetEl.getAttribute('data-type');
            const draggedType = dragged.dataset.type || dragged.getAttribute('data-type') || self.draggedType;

            if (!draggedType) return false;

            if (targetType === 'row') {
              return draggedType === 'column';
            }

            if (targetType === 'column') {
              const isStructural = ['section', 'container', 'row', 'column'].includes(draggedType);
              return !isStructural || draggedType === 'row';
            }

            if (targetType === 'root') return draggedType === 'section';
            if (targetType === 'section') return draggedType === 'container';
            if (targetType === 'container') return draggedType === 'row' || !['section', 'container', 'row', 'column'].includes(draggedType);

            const isStructural = ['section', 'container', 'row', 'column'].includes(draggedType);
            return !isStructural;
          }
        },
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        draggable: '.pb-block,.pb-component-item',
        onStart: (event) => {
          const type = event.item.dataset.type || event.item.getAttribute('data-type');
          self.draggedType = type;

          if (type === 'row') {
            self.canvasElement.classList.add('pb-dragging-row');
          } else if (type === 'column') {
            self.canvasElement.classList.add('pb-dragging-column');
          } else {
            self.canvasElement.classList.add('pb-dragging-content');
          }
        },
        onMove: (event) => {
          const container = event.to;
          if (!container) return false;

          const targetType = container.getAttribute('data-type') || container.dataset.type;
          const dragged = event.dragged;
          const draggedType = dragged.getAttribute('data-type') || (dragged.dataset ? dragged.dataset.type : null) || self.draggedType;

          if (!draggedType) return true;

          const isStructural = ['section', 'container', 'row', 'column'].includes(draggedType);

          if (targetType === 'row' && draggedType !== 'column') return false;
          if (targetType === 'column' && isStructural && draggedType !== 'row') return false;
          if (draggedType === 'column' && targetType !== 'row') return false;
          if (draggedType === 'row' && targetType !== 'container' && targetType !== 'column') return false;
          if (draggedType === 'container' && targetType !== 'section') return false;
          if (draggedType === 'section' && targetType !== 'root') return false;

          return true;
        },
        onEnd: () => {
          self.draggedType = null;
          self.canvasElement.classList.remove('pb-dragging-column', 'pb-dragging-content', 'pb-dragging-row');
        },
        onAdd: (event) => {
          const itemElement = event.item;
          const container = event.to;

          const parentId = container.dataset.parent;
          const parentInfo = parentId ? self.findComponent(parentId) : { component: { children: self.layout.components }, list: self.layout.components };

          let actualIndex = 0;
          const children = Array.from(container.children);
          for (let i = 0; i < children.indexOf(itemElement); i++) {
            if (children[i].classList.contains('pb-block')) actualIndex++;
          }

          if (itemElement.classList.contains('pb-component-item')) {
            const type = itemElement.dataset.type;
            const targetType = container.dataset.type || container.getAttribute('data-type');

            if (targetType === 'row' && type !== 'column') {
              itemElement.remove();
              return;
            }
            if (targetType === 'column' && ['section', 'container', 'row', 'column'].includes(type) && type !== 'row') {
              itemElement.remove();
              return;
            }

            let component = null;

            if (type === 'column') {
              if (!parentInfo || parentInfo.component.type !== 'row') {
                component = self.createComponent('row');
                const column = self.createComponent('column', { size: { md: 12 } }, true);
                component.children = [column];
              } else {
                let sum = 0;
                (parentInfo.component.children || []).forEach((child) => {
                  if (child.type === 'column' && child.props && child.props.size) {
                    sum += parseInt(child.props.size.md || 12);
                  }
                });
                let remaining = 12 - (sum % 12);
                if (remaining === 0) remaining = 12;
                component = self.createComponent('column', { size: { md: remaining } });
              }
            } else {
              component = self.createComponent(type);
            }

            if (component) {
              if (parentInfo) {
                if (!parentInfo.component.children) parentInfo.component.children = [];

                parentInfo.component.children.splice(actualIndex, 0, component);
                self.saveToHistory();
                self.render();
                self.selectComponent(component.id);
              }
            }
          } else {
            const id = itemElement.dataset.id;
            if (!id) return;

            const info = self.findComponent(id);
            if (!info) return;

            info.list.splice(info.index, 1);

            if (parentInfo) {
              if (!parentInfo.component.children) parentInfo.component.children = [];
              parentInfo.component.children.splice(actualIndex, 0, info.component);
            }

            self.saveToHistory();
            self.render();
            self.selectComponent(id);
          }
        },
        onEnd: (event) => {
          self.draggedType = null;
          self.canvasElement.classList.remove('pb-dragging-column', 'pb-dragging-content', 'pb-dragging-row');

          if (event.from === event.to) {
            const parentId = event.from.dataset.parent;
            const info = parentId ? self.findComponent(parentId) : { component: { children: self.layout.components } };
            const list = parentId ? (info ? info.component.children : null) : self.layout.components;

            if (list) {
              const item = list.splice(event.oldIndex, 1)[0];
              list.splice(event.newIndex, 0, item);
              self.saveToHistory();
              self.render();
            }
          }
        }
      });
    });

    // Root level sortable
    const root = this.canvasElement.querySelector('.pb-drop-root');
    if (root) {
      if (root._sortable) root._sortable.destroy();

      root._sortable = new Sortable(root, {
        group: {
          name: 'pb-components',
          put: (to, from, dragged) => {
            const draggedType = dragged.dataset.type || dragged.getAttribute('data-type') || self.draggedType;
            return !!self.componentDefinitions[draggedType];
          }
        },
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        draggable: '.pb-block,.pb-component-item',
        onAdd: (event) => {
          const itemElement = event.item;
          const container = event.to;
          const type = itemElement.dataset.type || itemElement.getAttribute('data-type') || self.draggedType;
          self.canvasElement.classList.remove('pb-root-drag-over');

          if (!self.componentDefinitions[type]) {
            itemElement.remove();
            return;
          }

          let actualIndex = 0;
          const children = Array.from(container.children);
          for (let i = 0; i < children.indexOf(itemElement); i++) {
            if (children[i].classList.contains('pb-block')) actualIndex++;
          }

          if (itemElement.classList.contains('pb-component-item')) {
            const type = itemElement.dataset.type;
            const component = self.wrapComponentForRoot(type);

            if (component) {
              self.layout.components.splice(actualIndex, 0, component);
              self.saveToHistory();
              self.render();
              self.selectComponent(component.id);
            }
          } else {
            const id = itemElement.dataset.id;
            if (!id) return;

            const info = self.findComponent(id);
            if (!info) return;

            info.list.splice(info.index, 1);
            self.layout.components.splice(actualIndex, 0, info.component);

            self.saveToHistory();
            self.render();
            self.selectComponent(id);
          }
        },
        onEnd: (event) => {
          self.canvasElement.classList.remove('pb-root-drag-over');
          if (event.from === event.to) {
            const list = self.layout.components;
            const item = list.splice(event.oldIndex, 1)[0];
            list.splice(event.newIndex, 0, item);
            self.saveToHistory();
            self.render();
          }
        },
        onMove: () => {
          self.canvasElement.classList.add('pb-root-drag-over');
          return true;
        }
      });

      root.addEventListener('dragenter', () => {
        if (self.draggedType) self.canvasElement.classList.add('pb-root-drag-over');
      });
      root.addEventListener('dragleave', (event) => {
        if (!root.contains(event.relatedTarget)) {
          self.canvasElement.classList.remove('pb-root-drag-over');
        }
      });
    }

    // Sidebar Sortable
    if (!this.sidebarElement._sortable) {
      this.sidebarElement._sortable = new Sortable(this.sidebarElement, {
        group: {
          name: 'pb-components',
          pull: 'clone',
          put: false
        },
        sort: false,
        draggable: '.pb-component-item',
        animation: 150,
        onStart: (event) => {
          const type = event.item.dataset.type || event.item.getAttribute('data-type');
          self.draggedType = type;

          if (type === 'row') {
            self.canvasElement.classList.add('pb-dragging-row');
          } else if (type === 'column') {
            self.canvasElement.classList.add('pb-dragging-column');
          } else {
            self.canvasElement.classList.add('pb-dragging-content');
          }
        },
        onEnd: () => {
          self.draggedType = null;
          self.canvasElement.classList.remove('pb-dragging-column', 'pb-dragging-content', 'pb-dragging-row', 'pb-root-drag-over');
        }
      });
    }
  };

  /**
   * Initialize Column Resizing handles
   */
  Prototype.initializeColumnResizing = function () {
    const self = this;
    this.canvasElement.querySelectorAll('.pb-col-resize').forEach((handle) => {
      handle.addEventListener('mousedown', (event) => {
        event.preventDefault();
        event.stopPropagation();

        const columnId = handle.dataset.colId;
        const columnBlock = handle.closest('.pb-block[data-type="column"]');
        const rowDropzone = columnBlock.parentElement;
        const columnBlocks = Array.from(rowDropzone.querySelectorAll(':scope > .pb-block[data-type="column"]'));
        const columnIdx = columnBlocks.indexOf(columnBlock);

        if (columnIdx < 0) return;

        const rowWidth = rowDropzone.getBoundingClientRect().width;
        const columnUnit = rowWidth / 12;
        const startX = event.clientX;

        const currentInfo = self.findComponent(columnId);
        if (!currentInfo) return;

        const startSize = (currentInfo.component.props.size && currentInfo.component.props.size.md) || 6;
        const sizeBadge = columnBlock.querySelector('.pb-col-size-badge');

        document.body.classList.add('pb-resizing-active');
        handle.classList.add('pb-resizing');

        const onMouseMove = (moveEvent) => {
          const deltaX = moveEvent.clientX - startX;
          let newSize = Math.round(startSize + deltaX / columnUnit);
          newSize = Math.max(1, Math.min(12, newSize));

          columnBlock.style.flex = `0 0 ${((newSize / 12) * 100).toFixed(2)}%`;
          columnBlock.style.maxWidth = `${((newSize / 12) * 100).toFixed(2)}%`;
          if (sizeBadge) sizeBadge.textContent = `${newSize}/12`;
          handle._pendingSize = newSize;
        };

        const onMouseUp = () => {
          document.removeEventListener('mousemove', onMouseMove);
          document.removeEventListener('mouseup', onMouseUp);
          document.body.classList.remove('pb-resizing-active');
          handle.classList.remove('pb-resizing');

          const finalSize = handle._pendingSize;
          if (finalSize) {
            if (!currentInfo.component.props.size) currentInfo.component.props.size = {};
            currentInfo.component.props.size.md = finalSize;
            self.saveToHistory();
            self.render();
            if (self.selectedId === columnId) self.renderProperties();
          }
        };

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
      });
    });
  };

  /**
   * Render a list of components recursively
   */
  Prototype.renderBlocks = function (components) {
    let html = '';
    if (!components || !Array.isArray(components)) return html;
    for (let i = 0; i < components.length; i++) {
      if (components[i]) html += this.renderBlock(components[i]);
    }
    return html;
  };

  /**
   * Render a single component block
   */
  Prototype.renderBlock = function (component) {
    if (!component || !component.type) return '';
    const definition = this.componentDefinitions[component.type] || {};
    const isSelected = this.selectedId === component.id ? ' pb-selected' : '';
    let inlineStyle = '';
    let classes = `pb-block${isSelected}`;
    const props = component.props || {};

    if (component.type === 'column') {
      const sizes = props.size || {};
      const smSize = parseInt(sizes.sm || 12);
      const mdSize = parseInt(sizes.md || smSize || 12);
      const lgSize = parseInt(sizes.lg || mdSize || smSize || 12);
      const activeSize = lgSize > 0 && lgSize <= 12 ? lgSize : mdSize;
      if (activeSize > 0 && activeSize <= 12) {
        const percentage = ((activeSize / 12) * 100).toFixed(4);
        inlineStyle = ` style="flex:0 0 ${percentage}%;max-width:${percentage}%"`;
      }
      if (smSize > 0 && smSize <= 12) classes += ` pb-col-sm-${smSize}`;
      if (mdSize > 0 && mdSize <= 12) classes += ` pb-col-md-${mdSize}`;
      if (lgSize > 0 && lgSize <= 12) classes += ` pb-col-lg-${lgSize}`;
      if (props.align_self) classes += ` ${props.align_self}`;
    }

    if (props['class']) classes += ` ${props['class']}`;

    let group = 'pb-content';
    if (component.type === 'section') group = 'pb-sec';
    else if (component.type === 'container') group = 'pb-con';
    else if (component.type === 'row') group = 'pb-row';
    else if (component.type === 'column') group = 'pb-col';

    const icon = definition.icon || '';
    const label = phrase(definition.label || component.type);

    let html = `<div class="${classes}" data-type="${component.type}" data-id="${component.id}" data-group="${group}"${inlineStyle}>`;

    // Label and actions
    html += `<span class="pb-block-label"><i class="${icon} me-1"></i>${label}</span>`;
    html += `
      <div class="pb-block-actions">
        <button data-act="up" data-bs-toggle="tooltip" title="${phrase('Move Up')}"><i class="mdi mdi-chevron-up"></i></button>
        <button data-act="down" data-bs-toggle="tooltip" title="${phrase('Move Down')}"><i class="mdi mdi-chevron-down"></i></button>
        <button data-act="dup" data-bs-toggle="tooltip" class="pb-act-dup" title="${phrase('Duplicate')}"><i class="mdi mdi-content-copy"></i></button>
        <button data-act="del" data-bs-toggle="tooltip" class="pb-act-del" title="${phrase('Delete')}"><i class="mdi mdi-delete"></i></button>
      </div>
    `;

    // Column specific controls
    if (component.type === 'column') {
      const sizeValue = component.props && component.props.size && component.props.size.md ? component.props.size.md : 'auto';
      html += `<span class="pb-col-size-badge">${sizeValue}/12</span>`;
      html += `<div class="pb-col-resize" data-col-id="${component.id}"></div>`;
    }

    // Content preview
    html += `<div class="pb-block-content">${this.renderContent(component)}</div>`;

    // Children dropzone
    if (definition.children) {
      let dropzoneClasses = 'pb-dropzone';
      if (component.type === 'row') {
        dropzoneClasses += ' pb-drop-row';
        if (props.align_items) dropzoneClasses += ` ${props.align_items}`;
        if (props.justify_content) dropzoneClasses += ` ${props.justify_content}`;
      } else if (component.type === 'column') {
        dropzoneClasses += ' pb-drop-col';
      }
      html += `<div class="${dropzoneClasses}" data-parent="${component.id}" data-type="${component.type}">`;
      if (component.children && component.children.length) {
        html += this.renderBlocks(component.children);
      }
      html += '</div>';
    }

    html += '</div>';
    return html;
  };

  /**
   * Render internal content of a component for preview
   */
  Prototype.renderContent = function (component) {
    const props = component.props || {};

    switch (component.type) {
      case 'heading': {
        const level = props.level || 2;
        const align = props.alignment || 'left';
        const text = phrase(props.text || 'Heading');
        return `<h${level} style="margin:4px 0;text-align:${align}">${text}</h${level}>`;
      }
      case 'paragraph': {
        const align = props.alignment || 'left';
        const text = this.markdownToHtml(props.text || 'Paragraph text...');
        return `<div style="text-align:${align}">${text}</div>`;
      }
      case 'alert': {
        const style = props.style || 'primary';
        const text = this.markdownToHtml(props.text || 'Alert message...');
        return `<div class="alert alert-${style} m-0">${text}</div>`;
      }
      case 'divider':
        return '<hr/>';
      case 'button': {
        const icon = props.icon ? `<i class="${props.icon}${props.text ? (props.icon_placement === 'suffix' ? ' ms-2' : ' me-2') : ''}"></i>` : '';
        const btnText = phrase(props.text || 'Button');
        const btnContent = props.icon_placement === 'suffix' ? btnText + icon : icon + btnText;
        const style = props.style || 'primary';
        const rounded = props.rounded ? ' rounded-pill' : '';
        const extraClass = props.class ? ` ${props.class}` : '';
        return `<span class="btn btn-${style} btn-sm${rounded}${extraClass}" style="pointer-events:none">${btnContent}</span>`;
      }
      case 'image': {
        if (props.src) {
          return `<img src="${props.src}" alt="${phrase(props.alt || '')}" class="img-fluid" style="border-radius:8px"/>`;
        }
        return `
          <div class="text-center text-muted py-3">
            <i class="mdi mdi-image mdi-3x"></i><br>
            <small>${phrase('No image selected')}</small>
          </div>
        `;
      }
      case 'video': {
        return `
          <div class="text-center text-muted py-3">
            <i class="mdi mdi-video mdi-3x"></i><br>
            <small>${phrase(props.url || 'No Video URL')}</small>
          </div>
        `;
      }
      case 'hero': {
        const heroBg = props.background ? (props.background.startsWith('#') || props.background.startsWith('rgb') ? props.background : `url(${props.background}) center/cover`) : 'linear-gradient(135deg,#667eea,#764ba2)';
        const overlay = props.background && props.overlay !== false ? '<div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);z-index:1"></div>' : '';
        const title = phrase(props.title || 'Hero Title');
        const subtitle = this.markdownToHtml(props.subtitle) || '';
        const button = props.button_text ? `<span class="btn btn-outline-secondary btn-sm rounded-pill">${phrase(props.button_text)}</span>` : '';

        return `
          <div class="text-center py-4 position-relative overflow-hidden" style="background:${heroBg};border-radius:8px;color:#fff;padding:24px">
            ${overlay}
            <div class="position-relative" style="z-index:2">
              <h3 class="fw-bold">${title}</h3>
              <p class="mb-2">${subtitle}</p>
              ${button}
            </div>
          </div>
        `;
      }
      case 'feature_box': {
        const icon = props.icon || 'mdi mdi-star';
        const title = phrase(props.title || 'Feature');
        const text = phrase(props.text || '');
        return `
          <div class="text-center py-2">
            <i class="${icon} mdi-2x d-block mb-1"></i>
            <strong>${title}</strong><br>
            <small class="text-muted">${text}</small>
          </div>
        `;
      }
      case 'card': {
        const cardImg = props.image ? `<img src="${props.image}" class="img-fluid rounded-top" style="width:100%;height:150px;object-fit:cover"/>` : '';
        const title = phrase(props.title || 'Card Title');
        const text = this.markdownToHtml(props.text) || '';
        return `
          ${cardImg}
          <div class="p-3">
            <strong>${title}</strong>
            <div class="text-muted small">${text}</div>
          </div>
        `;
      }
      case 'spacer': {
        const height = props.height || 40;
        return `<div style="height:${height}px;background:repeating-linear-gradient(45deg,transparent,transparent 5px,var(--pb-spacer-pattern) 5px,var(--pb-spacer-pattern) 10px);border-radius:4px"></div>`;
      }
      case 'accordion': {
        let accHtml = '';
        (props.items || []).forEach((item) => {
          accHtml += `
            <div class="border rounded p-2 mb-1">
              <strong>${phrase(item.title)}</strong><br>
              <small class="text-muted">${phrase(this.markdownToHtml(item.body))}</small>
            </div>
          `;
        });
        return accHtml;
      }
      case 'carousel': {
        let carHtml = `
          <div class="bg-body-tertiary text-body rounded p-3 text-center">
            <i class="mdi mdi-view-carousel-outline mdi-2x"></i>
            <strong>${phrase('Carousel Slider')}</strong>
            <div class="mt-2 small">
        `;
        (props.items || []).forEach((item, i) => {
          carHtml += `<span class="badge bg-secondary me-1">${i + 1}</span>`;
        });
        return carHtml + '</div></div>';
      }
      case 'tabs': {
        let tabHtml = `
          <div class="bg-body-tertiary text-body rounded p-2">
            <strong>${phrase('Tabs')} (${phrase(props.alignment || 'horizontal')})</strong>
            <div class="d-flex gap-1 mt-1">
        `;
        (props.items || []).forEach((item) => {
          tabHtml += `<div class="border rounded px-2 py-1 small bg-body text-body">${phrase(item.title)}</div>`;
        });
        return tabHtml + '</div></div>';
      }
      case 'pricing': {
        const priceFeatured = props.featured ? ' border-primary' : '';
        const title = phrase(props.title || 'Plan');
        const price = props.price || '$0';
        const period = phrase(props.period || '');
        const btnText = phrase(props.btn_text || 'Buy Now');
        return `
          <div class="border rounded p-3 text-center bg-body-tertiary text-body${priceFeatured}">
            <h5 class="fw-bold mb-1">${title}</h5>
            <h4 class="mb-0">${price}</h4>
            <small class="text-muted d-block mb-2">${period}</small>
            <span class="btn btn-primary btn-sm rounded-pill">${btnText}</span>
          </div>
        `;
      }
      case 'testimonial': {
        const testImg = props.image ? `<img src="${props.image}" class="rounded-circle me-2" style="width:30px;height:30px;object-fit:cover"/>` : '<div class="rounded-circle bg-secondary me-2" style="width:30px;height:30px"></div>';
        const quote = phrase(this.markdownToHtml(props.quote) || '...');
        const author = phrase(props.author || '');
        return `
          <div class="bg-body-tertiary text-body rounded p-3 italic">
            ${quote}
            <div class="mt-2 d-flex align-items-center">
              ${testImg}
              <strong>${author}</strong>
            </div>
          </div>
        `;
      }
      case 'team_member': {
        const memberImg = props.image
          ? `<img src="${props.image}" class="rounded-circle mx-auto mb-2" style="width:60px;height:60px;object-fit:cover;display:block"/>`
          : '<div class="rounded-circle bg-secondary mx-auto mb-2" style="width:60px;height:60px"></div>';
        const name = phrase(props.name || '');
        const role = phrase(props.role || '');
        return `
          <div class="text-center">
            ${memberImg}
            <strong>${name}</strong><br>
            <small class="text-primary">${role}</small>
          </div>
        `;
      }
      case 'cta': {
        const ctaBg = props.background === 'primary' ? 'linear-gradient(135deg,#0d6efd,#0b5ed7)' : props.background === 'dark' ? '#212529' : '#f8f9fa';
        const ctaColor = props.background === 'light' ? '#212529' : '#fff';
        const ctaRounding = props.class && props.class.indexOf('rounded') > -1 ? '' : 'rounded-4';
        const extraClass = props.class ? ` ${props.class}` : '';
        const title = phrase(props.title || 'CTA Title');
        const text = phrase(props.text || 'Join thousands of satisfied customers.');
        const btnStyle = props.background === 'light' ? 'primary' : 'light';
        const btnText = phrase(props.button_text || 'Join Now');

        return `
          <div class="d-flex justify-content-between align-items-center py-4 px-5 text-start ${ctaRounding}${extraClass}" style="background:${ctaBg};color:${ctaColor};min-height:120px">
            <div>
              <h4 class="fw-bold mb-1">${title}</h4>
              <p class="mb-0 opacity-75">${text}</p>
            </div>
            <div>
              <span class="btn btn-${btnStyle} rounded-pill px-4 py-2 fw-bold">${btnText}</span>
            </div>
          </div>
        `;
      }
      default:
        if (['section', 'container', 'row', 'column'].indexOf(component.type) !== -1) return '';
        return `<div class="text-muted text-center py-2"><small>${phrase(component.type)}</small></div>`;
    }
  };

  /**
   * Strict WYSIWYG HTML Sanitizer
   */
  Prototype.sanitizeWysiwygHtml = function (html) {
    if (!html) return '';
    try {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const allowed = ['STRONG', 'B', 'I', 'EM', 'U', 'DEL', 'S', 'STRIKE', 'STRIKETHROUGH', 'A', 'OL', 'UL', 'LI', 'P', 'BR'];

      const clean = function (parent) {
        const nodes = Array.from(parent.childNodes);
        nodes.forEach((node) => {
          if (node.nodeType === 1) {
            if (allowed.indexOf(node.tagName) === -1) {
              while (node.firstChild) {
                parent.insertBefore(node.firstChild, node);
              }
              parent.removeChild(node);
            } else {
              const attrs = Array.from(node.attributes);
              attrs.forEach((attr) => {
                const name = attr.name.toLowerCase();
                if (name.startsWith('on') || name === 'style' || name === 'id') {
                  node.removeAttribute(attr.name);
                }
              });

              if (node.tagName === 'A') {
                const href = (node.getAttribute('href') || '').trim();
                if (/^(javascript|data|vbscript|file):/i.test(href)) {
                  node.setAttribute('href', '#');
                }
              }

              clean(node);
            }
          }
        });
      };

      clean(doc.body);
      return doc.body.innerHTML;
    } catch (e) {
      return html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
    }
  };

  /**
   * Simple HTML to Markdown converter
   */
  Prototype.htmlToMarkdown = function (html) {
    if (!html) return '';
    let md = this.sanitizeWysiwygHtml(html);
    md = md.replace(/<b>(.*?)<\/b>|<strong>(.*?)<\/strong>/gi, '**$1$2**');
    md = md.replace(/<i>(.*?)<\/i>|<em>(.*?)<\/em>/gi, '_$1$2_');
    md = md.replace(/<u>(.*?)<\/u>/gi, '<u>$1</u>');
    md = md.replace(/<strike>(.*?)<\/strike>|<s>(.*?)<\/s>|<del>(.*?)<\/del>/gi, '~~$1$2$3~~');
    md = md.replace(/<a.*?href="(.*?)".*?>(.*?)<\/a>/gi, '[$2]($1)');
    md = md.replace(/<ul>(.*?)<\/ul>/gi, (m, c) => c.replace(/<li>(.*?)<\/li>/gi, (m2, c2) => '* ' + c2 + '\n'));
    md = md.replace(/<ol>(.*?)<\/ol>/gi, (m, c) => {
      let i = 1;
      return c.replace(/<li>(.*?)<\/li>/gi, (m2, c2) => i++ + '. ' + c2 + '\n');
    });
    md = md.replace(/<br\s*\/?>/gi, '\n');
    md = md.replace(/<p>(.*?)<\/p>/gi, '$1\n\n');
    md = md.replace(/&nbsp;/g, ' ');
    md = md.replace(/<.*?>/g, '');
    return md.trim();
  };

  /**
   * Simple Markdown to HTML converter
   */
  Prototype.markdownToHtml = function (md) {
    if (!md) return '';
    let html = md;
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/_(.*?)_/g, '<em>$1</em>');
    html = html.replace(/~~(.*?)~~/g, '<del>$1</del>');
    html = html.replace(/\[(.*?)\]\((.*?)\)/g, (match, text, url) => {
      const cleanUrl = url.trim();
      if (/^(javascript|data|vbscript|file):/i.test(cleanUrl)) return `<a href="#">${text}</a>`;
      return `<a href="${cleanUrl}" target="_blank">${text}</a>`;
    });

    // Group Unordered Lists
    html = html.replace(/^\s?\*\s*(.*?)$/gm, '<li class="_ul">$1</li>');
    html = html.replace(/(?:<li class="_ul">.*?<\/li>\n?)+/g, (m) => '<ul>' + m.replace(/ class="_ul"/g, '').replace(/\n/g, '') + '</ul>');

    // Group Ordered Lists
    html = html.replace(/^\s?(\d+)\.\s*(.*?)$/gm, '<li class="_ol">$2</li>');
    html = html.replace(/(?:<li class="_ol">.*?<\/li>\n?)+/g, (m) => '<ol>' + m.replace(/ class="_ol"/g, '').replace(/\n/g, '') + '</ol>');

    html = html.replace(/\n\n/g, '</p><p>').replace(/\n/g, '<br>');
    if (html.indexOf('<p>') === -1 && html.length > 0) html = `<p>${html}</p>`;
    return html;
  };

  /**
   * Find a component and its context (parent, index) by ID
   */
  Prototype.findComponent = function (id, components, parent) {
    components = components || this.layout.components;
    for (let i = 0; i < components.length; i++) {
      if (!components[i]) continue;
      if (components[i].id === id) {
        return { component: components[i], list: components, index: i, parent: parent };
      }
      if (components[i].children) {
        const result = this.findComponent(id, components[i].children, components[i]);
        if (result) return result;
      }
    }
    return null;
  };

  /**
   * Find a component object by ID anywhere in the layout
   */
  Prototype.findAnywhere = function (id) {
    const info = this.findComponent(id);
    return info ? info.component : null;
  };

  /**
   * Remove a component from its parent list
   */
  Prototype.removeFromParent = function (id, parentId) {
    if (parentId) {
      const parentInfo = this.findComponent(parentId);
      if (parentInfo && parentInfo.component.children) {
        const list = parentInfo.component.children;
        for (let i = 0; i < list.length; i++) {
          if (list[i].id === id) {
            list.splice(i, 1);
            return;
          }
        }
      }
    } else {
      const list = this.layout.components;
      for (let i = 0; i < list.length; i++) {
        if (list[i].id === id) {
          list.splice(i, 1);
          return;
        }
      }
    }
  };

  /**
   * Select a component by ID and show its properties
   */
  Prototype.selectComponent = function (id) {
    this.selectedId = id;
    this.renderProperties();
    this.highlightComponent();
  };

  /**
   * Highlight the selected component in the canvas
   */
  Prototype.highlightComponent = function () {
    this.canvasElement.querySelectorAll('.pb-block').forEach((block) => {
      block.classList.remove('pb-selected');
    });
    if (this.selectedId) {
      const element = this.canvasElement.querySelector(`[data-id="${this.selectedId}"]`);
      if (element) element.classList.add('pb-selected');
    }
  };

  /**
   * Delete a component by ID
   */
  Prototype.deleteComponent = function (id) {
    const info = this.findComponent(id);
    if (info) {
      info.list.splice(info.index, 1);
      this.selectedId = null;
      this.saveToHistory();
      this.render();
      this.renderProperties();
    }
  };

  /**
   * Duplicate a component by ID
   */
  Prototype.duplicateComponent = function (id) {
    const info = this.findComponent(id);
    if (!info) return;

    const clone = JSON.parse(JSON.stringify(info.component));
    this.regenerateAllIds(clone);

    info.list.splice(info.index + 1, 0, clone);
    this.saveToHistory();
    this.render();
    this.selectComponent(clone.id);
  };

  /**
   * Regenerate IDs for a component and all its children
   */
  Prototype.regenerateAllIds = function (component) {
    if (!component) return;
    component.id = this.generateUniqueId();
    if (component.children && Array.isArray(component.children)) {
      component.children.forEach((child) => child && this.regenerateAllIds(child));
    }
  };

  /**
   * Move a component within its current list (up/down)
   */
  Prototype.moveComponent = function (id, direction) {
    const info = this.findComponent(id);
    if (!info) return;

    const newIndex = info.index + direction;
    if (newIndex < 0 || newIndex >= info.list.length) return;

    const temp = info.list[info.index];
    info.list[info.index] = info.list[newIndex];
    info.list[newIndex] = temp;

    this.saveToHistory();
    this.render();
  };

  /**
   * Move a component from one parent to another (or same) at specific index
   */
  Prototype.transferComponent = function (itemId, fromParentId, toParentId, oldIndex, newIndex) {
    const info = this.findComponent(itemId);
    if (!info) return;

    const item = info.list.splice(info.index, 1)[0];

    if (toParentId) {
      const targetParentInfo = this.findComponent(toParentId);
      if (targetParentInfo && targetParentInfo.component.children) {
        targetParentInfo.component.children.splice(newIndex, 0, item);
      }
    } else {
      this.layout.components.splice(newIndex, 0, item);
    }

    this.saveToHistory();
    this.render();
  };

  /**
   * Render the properties panel for the selected component
   */
  Prototype.renderProperties = function () {
    if (!this.selectedId) {
      this.propertiesElement.innerHTML = `
        <div class="pb-props-empty">
          <i class="mdi mdi-cursor-default-click mdi-3x d-block mb-2"></i>
          ${phrase('Select a component to edit its properties')}
        </div>
      `;
      return;
    }

    const info = this.findComponent(this.selectedId);
    if (!info) {
      this.propertiesElement.innerHTML = '';
      return;
    }

    const component = info.component;
    if (!component.props) component.props = {};
    const definition = this.componentDefinitions[component.type] || {};
    const grouping = definition.grouping || [];
    const defaults = definition.defaults || {};
    const props = component.props || {};

    const icon = definition.icon || '';
    const label = phrase(definition.label || component.type);

    let html = `<div class="pb-props-header"><i class="${icon} me-2"></i>${label}</div>`;

    const renderedKeys = new Set();

    // Render Groups first
    grouping.forEach((group) => {
      html += '<div class="row g-2 px-3 mt-2">';
      group.forEach((key) => {
        if (key in defaults) {
          const value = props[key] !== undefined ? props[key] : defaults[key];
          const options = definition.options ? definition.options[key] : null;
          html += `<div class="col">${this.renderPropertyField(key, value, options, true, component.type)}</div>`;
          renderedKeys.add(key);
        }
      });
      html += '</div>';
    });

    // Render remaining fields
    for (const key in defaults) {
      if (key === 'items' || renderedKeys.has(key)) continue;
      const value = props[key] !== undefined ? props[key] : defaults[key];
      const options = definition.options ? definition.options[key] : null;
      html += this.renderPropertyField(key, value, options, false, component.type);
    }

    if (defaults.items) html += this.renderItemsManager(component);
    if (!('class' in defaults) && !renderedKeys.has('class')) html += this.renderPropertyField('class', props['class'] || '');

    this.propertiesElement.innerHTML = html;
    this.bindPropertyEvents(component);
    this.initializeWysiwyg(component);
    this.initTooltips();

    // Reactivate plugins (Icon Picker, Select2, etc)
    if (typeof reactivate === 'function') {
      reactivate(['iconpicker', 'select']);
    }
  };

  /**
   * Render a single property input field
   */
  Prototype.renderPropertyField = function (key, value, options, isInsideRow, componentType) {
    const label = phrase(key.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase()));
    const fieldId = `pb_field_${this.selectedId || 'prop'}_${key.replace(/\./g, '_')}`;
    let html = isInsideRow ? '<div class="mb-2">' : '<div class="pb-props-group">';
    html += `<label for="${fieldId}">${label}</label>`;

    const selectRole = this.classes.selectRole ? ` data-role="${this.classes.selectRole}"` : '';

    if (options === 'colorpicker') {
      const colorClass = `${this.classes.color || 'form-control form-control-color'} pb-property-input w-100`;
      html += `<input type="color" id="${fieldId}" name="${fieldId}" data-key="${key}" class="${colorClass}" value="${value || '#000000'}">`;
    } else if (options && typeof options === 'object') {
      const selectClass = `${this.classes.select || 'form-control form-control-sm'} pb-property-input`;
      html += `<select id="${fieldId}" name="${fieldId}" data-key="${key}" class="${selectClass}"${selectRole}>`;
      for (const optKey in options) {
        const selected = value == optKey ? ' selected' : '';
        html += `<option value="${optKey}"${selected}>${phrase(options[optKey])}</option>`;
      }
      html += '</select>';
    } else if (options === 'boolean' || typeof value === 'boolean') {
      const selectClass = `${this.classes.select || 'form-control form-control-sm'} pb-property-input`;
      const yesSelected = value ? ' selected' : '';
      const noSelected = !value ? ' selected' : '';
      html += `
        <select id="${fieldId}" name="${fieldId}" data-key="${key}" class="${selectClass}"${selectRole}>
          <option value="1"${yesSelected}>${phrase('Yes')}</option>
          <option value="0"${noSelected}>${phrase('No')}</option>
        </select>
      `;
    } else if (typeof value === 'object' && !Array.isArray(value)) {
      const numberClass = `${this.classes.number || this.classes.input || 'form-control form-control-sm'} pb-property-input`;
      html += '<div class="d-flex gap-1">';
      ['sm', 'md', 'lg'].forEach((bp) => {
        const val = value[bp] || '';
        const bpId = `${fieldId}_${bp}`;
        html += `<input type="number" id="${bpId}" name="${bpId}" data-key="${key}.${bp}" class="${numberClass}" placeholder="${bp.toUpperCase()}" value="${val}" min="1" max="12" style="width:60px">`;
      });
      html += '</div>';
    } else if (options === 'textarea' || key === 'description') {
      const textareaClass = `${this.classes.textarea || 'form-control form-control-sm'} pb-property-input`;
      html += `<textarea id="${fieldId}" name="${fieldId}" data-key="${key}" class="${textareaClass}">${value || ''}</textarea>`;
    } else if (['text', 'subtitle', 'body', 'bio', 'quote'].indexOf(key) > -1 && componentType !== 'button') {
      const wysiwygClass = `${this.classes.wysiwyg || 'pb-wysiwyg'}`;
      const wysiwygEditorClass = `${this.classes.wysiwygEditor || 'pb-wysiwyg-editor'}`;
      const content = this.markdownToHtml(value) || '';
      html += `
        <div class="${wysiwygClass}" data-key="${key}">
          <div class="pb-wysiwyg-toolbar">
            <button type="button" data-cmd="bold" data-bs-toggle="tooltip" title="${phrase('Bold')}"><i class="mdi mdi-format-bold"></i></button>
            <button type="button" data-cmd="italic" data-bs-toggle="tooltip" title="${phrase('Italic')}"><i class="mdi mdi-format-italic"></i></button>
            <button type="button" data-cmd="underline" data-bs-toggle="tooltip" title="${phrase('Underline')}"><i class="mdi mdi-format-underline"></i></button>
            <button type="button" data-cmd="strikeThrough" data-bs-toggle="tooltip" title="${phrase('Strikethrough')}"><i class="mdi mdi-format-strikethrough"></i></button>
            <span class="pb-wys-sep"></span>
            <button type="button" data-cmd="createLink" data-bs-toggle="tooltip" title="${phrase('Insert Link')}"><i class="mdi mdi-link-variant"></i></button>
            <button type="button" data-cmd="unlink" data-bs-toggle="tooltip" title="${phrase('Remove Link')}"><i class="mdi mdi-link-variant-off"></i></button>
            <span class="pb-wys-sep"></span>
            <button type="button" data-cmd="insertUnorderedList" data-bs-toggle="tooltip" title="${phrase('Bullet List')}"><i class="mdi mdi-format-list-bulleted"></i></button>
            <button type="button" data-cmd="insertOrderedList" data-bs-toggle="tooltip" title="${phrase('Numbered List')}"><i class="mdi mdi-format-list-numbered"></i></button>
            <span class="pb-wys-sep"></span>
            <button type="button" data-cmd="removeFormat" data-bs-toggle="tooltip" title="${phrase('Clear Formatting')}"><i class="mdi mdi-format-clear"></i></button>
          </div>
          <div id="${fieldId}" class="${wysiwygEditorClass}" contenteditable="true" data-placeholder="${phrase('Type here...')}">${content}</div>
        </div>
      `;
    } else if (options === 'iconpicker' || key === 'icon') {
      const sanitizedVal = String(value || '').replace(/"/g, '&quot;');
      const iconVal = value || 'mdi mdi-radiobox-blank';
      const inputClass = `${this.classes.input || 'form-control form-control-sm'} pb-property-input`;
      html += `
        <div class="input-group input-group-sm">
          <button type="button" class="btn btn-secondary pb-icon-picker-btn" data-role="iconpicker" data-iconset="materialdesign" data-icon="${iconVal}" data-key="${key}"></button>
          <input type="text" id="${fieldId}" name="${fieldId}" data-key="${key}" class="${inputClass}" value="${sanitizedVal}" placeholder="mdi mdi-star" readonly>
        </div>
      `;
    } else if (key === 'src' || key === 'image' || key === 'background') {
      const sanitizedVal = String(value || '').replace(/"/g, '&quot;');
      const placeholder = phrase(key.charAt(0).toUpperCase() + key.slice(1) + ' URL');
      const uploaderUrl = this.options.uploaderUrl;
      const inputClass = `${this.classes.input || 'form-control form-control-sm'} pb-property-input`;
      html += `
        <div class="pb-prop-img-input input-group input-group-sm">
          <input type="text" id="${fieldId}" name="${fieldId}" data-key="${key}" class="${inputClass}" value="${sanitizedVal}" placeholder="${placeholder}">
          <a href="${uploaderUrl}" class="btn btn-sm btn-light pb-browse-img --modal" data-bs-toggle="tooltip" title="${phrase('Browse')}">
            <i class="mdi mdi-folder-open"></i>
          </a>
        </div>
      `;
    } else {
      const sanitizedVal = String(value || '').replace(/"/g, '&quot;');
      const inputClass = `${this.classes.input || 'form-control form-control-sm'} pb-property-input`;
      html += `<input type="text" id="${fieldId}" name="${fieldId}" data-key="${key}" class="${inputClass}" value="${sanitizedVal}">`;
    }

    html += '</div>';
    return html;
  };

  /**
   * Bind events to property inputs
   */
  Prototype.bindPropertyEvents = function (component) {
    const self = this;
    this.propertiesElement.querySelectorAll('.pb-property-input, .pb-icon-picker-btn').forEach((input) => {
      const updateValue = function () {
        if (!component.props) component.props = {};
        const key = input.dataset.key;
        let value = input.value;

        if (input.getAttribute('role') === 'iconpicker') {
          value = input.querySelector('input') ? input.querySelector('input').value : input.value || input.dataset.icon;
        }

        if (key.indexOf('.') > -1) {
          const parts = key.split('.');
          if (!component.props[parts[0]]) component.props[parts[0]] = {};
          if (value) component.props[parts[0]][parts[1]] = parseInt(value);
          else delete component.props[parts[0]][parts[1]];
        } else {
          if (input.tagName === 'SELECT' && (value === '1' || value === '0') && typeof component.props[key] === 'boolean') {
            value = value === '1';
          }
          component.props[key] = value;
        }

        self.saveToHistory();

        clearTimeout(self.debounceTimer);
        self.debounceTimer = setTimeout(() => {
          const structuralProps = ['class', 'align_items', 'justify_content', 'align_self', 'id'];
          if (structuralProps.indexOf(key) > -1) {
            self.render();
          } else {
            self.refreshBlockPreview(component);
          }
          self.syncToInput();
        }, 150);
      };

      if (input.tagName === 'SELECT') {
        input.addEventListener('change', updateValue);
      } else {
        input.addEventListener('input', updateValue);
        input.addEventListener('change', updateValue);
      }

      if (input.getAttribute('role') === 'iconpicker') {
        $(input).on('change', (e) => {
          if (e.icon) {
            input.value = e.icon;
            input.dataset.icon = e.icon;

            const textInput = input.nextElementSibling;
            if (textInput && textInput.tagName === 'INPUT') {
              textInput.value = e.icon;
            }

            updateValue();
          }
        });
      }
    });

    this.bindItemsManager(component);
  };

  /**
   * Initialize WYSIWYG editors
   */
  Prototype.initializeWysiwyg = function (component) {
    if (!component.props) component.props = {};
    const self = this;
    this.propertiesElement.querySelectorAll('.pb-wysiwyg').forEach((container) => {
      const key = container.dataset.key;
      const editor = container.querySelector('.pb-wysiwyg-editor');
      const toolbar = container.querySelector('.pb-wysiwyg-toolbar');

      toolbar.addEventListener('mousedown', (e) => e.preventDefault());
      toolbar.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-cmd]');
        if (!btn) return;

        const cmd = btn.dataset.cmd;
        const idx = container.dataset.itemIdx;
        editor.focus();

        if (cmd === 'createLink') {
          const selection = window.getSelection();
          let selectedText = '';
          let range = null;
          let existingLink = null;
          let existingUrl = 'https://';

          if (selection.rangeCount > 0) {
            range = selection.getRangeAt(0);
            selectedText = range.toString();

            let containerNode = range.commonAncestorContainer;
            if (containerNode.nodeType === 3) {
              containerNode = containerNode.parentNode;
            }
            const closestLink = containerNode.closest ? containerNode.closest('a') : null;

            if (closestLink && editor.contains(closestLink)) {
              existingLink = closestLink;
              selectedText = closestLink.textContent;
              existingUrl = closestLink.getAttribute('href') || 'https://';

              range = document.createRange();
              range.selectNode(closestLink);
              selection.removeAllRanges();
              selection.addRange(range);
            }
          }

          const modalId = `pb-link-modal-${Date.now()}`;
          const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">${phrase(existingLink ? 'Edit Link' : 'Insert Link')}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="pb-link-label" class="form-label">${phrase('Link Label')}</label>
                      <input type="text" class="form-control" id="pb-link-label" name="pb-link-label" value="${selectedText}" placeholder="${phrase('Text to display')}">
                    </div>
                    <div class="mb-3">
                      <label for="pb-link-url" class="form-label">${phrase('URL')}</label>
                      <input type="text" class="form-control" id="pb-link-url" name="pb-link-url" value="${existingUrl}" placeholder="https://">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${phrase('Cancel')}</button>
                    <button type="button" class="btn btn-primary pb-link-save">${phrase('Insert')}</button>
                  </div>
                </div>
              </div>
            </div>
          `;

          document.body.insertAdjacentHTML('beforeend', modalHtml);
          const modalEl = document.getElementById(modalId);
          const modal = new bootstrap.Modal(modalEl);
          modal.show();

          modalEl.addEventListener('shown.bs.modal', () => {
            if (selectedText) {
              document.getElementById('pb-link-url').focus();
            } else {
              document.getElementById('pb-link-label').focus();
            }
          });

          modalEl.querySelector('.pb-link-save').addEventListener('click', () => {
            const label = document.getElementById('pb-link-label').value || 'link';
            const url = document.getElementById('pb-link-url').value;

            if (url) {
              modal.hide();
              editor.focus();

              if (existingLink) {
                existingLink.textContent = label;
                existingLink.setAttribute('href', url);
              } else {
                const sel = window.getSelection();
                sel.removeAllRanges();
                if (range) {
                  sel.addRange(range);
                }

                if (selectedText && selectedText === label) {
                  document.execCommand('createLink', false, url);
                } else {
                  document.execCommand('insertHTML', false, `<a href="${url}">${label}</a>`);
                }
              }

              setTimeout(() => self.updateWysiwygToolbar(toolbar), 10);

              if (idx !== undefined) {
                if (!component.props.items) component.props.items = [];
                component.props.items[idx][key] = self.htmlToMarkdown(editor.innerHTML);
              } else {
                component.props[key] = self.htmlToMarkdown(editor.innerHTML);
              }
              self.saveToHistory();
              self.refreshBlockPreview(component);
            }
          });

          modalEl.addEventListener('hidden.bs.modal', () => {
            modalEl.remove();
          });

          return;
        } else {
          document.execCommand(cmd, false, null);
        }

        setTimeout(() => self.updateWysiwygToolbar(toolbar), 10);

        if (idx !== undefined) {
          if (!component.props.items) component.props.items = [];
          component.props.items[idx][key] = self.htmlToMarkdown(editor.innerHTML);
        } else {
          component.props[key] = self.htmlToMarkdown(editor.innerHTML);
        }
        self.saveToHistory();
        self.refreshBlockPreview(component);
      });

      let debounce = null;
      editor.addEventListener('input', () => {
        const idx = container.dataset.itemIdx;
        if (idx !== undefined) {
          if (!component.props.items) component.props.items = [];
          component.props.items[idx][key] = self.htmlToMarkdown(editor.innerHTML);
        } else {
          component.props[key] = self.htmlToMarkdown(editor.innerHTML);
        }

        self.saveToHistory();
        clearTimeout(debounce);
        debounce = setTimeout(() => self.refreshBlockPreview(component), 200);
      });

      document.execCommand('defaultParagraphSeparator', false, 'p');

      editor.addEventListener('keydown', (e) => {
        if (e.key === ' ') {
          const selection = window.getSelection();
          if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            const node = range.startContainer;

            if (node.nodeType === 3) {
              const text = node.textContent;
              const match = text.match(/^(\d+\.|-|\*)$/);
              if (match && range.startOffset === text.length) {
                e.preventDefault();

                const listType = match[1].includes('.') ? 'ol' : 'ul';
                const list = document.createElement(listType);
                const li = document.createElement('li');
                li.innerHTML = '<br>';
                list.appendChild(li);

                let block = node;
                while (block.parentNode && block.parentNode !== editor && !['P', 'DIV', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6'].includes(block.parentNode.tagName)) {
                  block = block.parentNode;
                }

                const parent = block.parentNode || editor;
                parent.replaceChild(list, block);

                const sel = window.getSelection();
                const newRange = document.createRange();
                newRange.setStart(li, 0);
                newRange.collapse(true);
                sel.removeAllRanges();
                sel.addRange(newRange);
              }
            }
          }
        }
      });

      editor.addEventListener('keyup', () => self.updateWysiwygToolbar(toolbar));
      editor.addEventListener('mouseup', () => self.updateWysiwygToolbar(toolbar));
    });
  };

  /**
   * Update WYSIWYG toolbar button states
   */
  Prototype.updateWysiwygToolbar = function (toolbar) {
    const commands = ['bold', 'italic', 'underline', 'strikeThrough'];
    toolbar.querySelectorAll('[data-cmd]').forEach((btn) => {
      if (commands.indexOf(btn.dataset.cmd) > -1) {
        btn.classList.toggle('active', document.queryCommandState(btn.dataset.cmd));
      }
    });
  };

  /**
   * Refresh a single block's preview on the canvas without full render
   */
  Prototype.refreshBlockPreview = function (component) {
    const blockElement = this.canvasElement.querySelector(`[data-id="${component.id}"]`);
    if (!blockElement) return;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = this.renderContent(component);

    const children = Array.from(blockElement.childNodes);
    children.forEach((node) => {
      const isControl =
        node.nodeType === 1 &&
        (node.classList.contains('pb-block-label') ||
          node.classList.contains('pb-block-actions') ||
          node.classList.contains('pb-dropzone') ||
          node.classList.contains('pb-block') ||
          node.classList.contains('pb-col-resize') ||
          node.classList.contains('pb-col-size-badge'));

      if (!isControl) node.remove();
    });

    const actionsDiv = blockElement.querySelector('.pb-block-actions');
    const refNode = actionsDiv ? actionsDiv.nextSibling : blockElement.firstChild;
    while (wrapper.firstChild) blockElement.insertBefore(wrapper.firstChild, refNode);

    this.syncToInput();
  };

  /**
   * Save current state to history for undo/redo
   */
  Prototype.saveToHistory = function () {
    this.history = this.history.slice(0, this.historyIndex + 1);
    this.history.push(JSON.stringify(this.layout));
    if (this.history.length > 50) this.history.shift();
    this.historyIndex = this.history.length - 1;
  };

  /**
   * Undo last action
   */
  Prototype.undo = function () {
    if (this.historyIndex > 0) {
      this.historyIndex--;
      this.layout = JSON.parse(this.history[this.historyIndex]);
      this.render();
      this.renderProperties();
    }
  };

  /**
   * Redo action
   */
  Prototype.redo = function () {
    if (this.historyIndex < this.history.length - 1) {
      this.historyIndex++;
      this.layout = JSON.parse(this.history[this.historyIndex]);
      this.render();
      this.renderProperties();
    }
  };

  /**
   * Sync layout data to hidden input
   */
  Prototype.syncToInput = function () {
    if (this.inputElement) this.inputElement.value = JSON.stringify(this.layout);
  };

  /**
   * Recursively clean null or invalid component items from layout tree
   */
  Prototype.sanitizeLayout = function (node) {
    if (!node || typeof node !== 'object') return node;
    if (Array.isArray(node.components)) {
      node.components = node.components.filter(Boolean).map((item) => this.sanitizeLayout(item));
    }
    if (Array.isArray(node.children)) {
      node.children = node.children.filter(Boolean).map((item) => this.sanitizeLayout(item));
    }
    return node;
  };

  /**
   * Load layout data from hidden input
   */
  Prototype.loadFromInput = function () {
    if (this.inputElement && this.inputElement.value) {
      try {
        const data = JSON.parse(this.inputElement.value);
        if (data && data.components) {
          this.layout = this.sanitizeLayout(data);
          this.layout.framework = this.layout.framework || this.framework;
          this.saveToHistory();
        }
      } catch (e) {
        this.saveToHistory();
      }
    } else {
      this.saveToHistory();
    }
  };

  /**
   * Open standalone preview
   */
  Prototype.preview = function () {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'preview';
    form.target = '_blank';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'layout';
    input.value = JSON.stringify(this.layout);

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
  };

  /**
   * Trigger form submission
   */
  Prototype.submitForm = function () {
    const form = this.container.closest('form');
    if (form) {
      const event = new Event('submit', { bubbles: true, cancelable: true });
      form.dispatchEvent(event);
    }
  };

  /**
   * Render Items Manager for list-based components (carousel, etc)
   */
  Prototype.renderItemsManager = function (component) {
    const definition = this.componentDefinitions[component.type] || {};
    const items = component.props.items || definition.defaults.items || [];
    const labelMap = { carousel: 'Slide', tabs: 'Tab', accordion: 'Accordion Item' };
    const itemLabel = phrase(labelMap[component.type] || 'Item');

    let html = `<div class="pb-items-manager"><span class="d-block mb-2 fw-bold">${phrase('Items')}</span>`;

    items.forEach((item, index) => {
      const upBtn = index > 0 ? `<button type="button" class="btn btn-sm btn-link text-secondary pb-item-up p-0" data-bs-toggle="tooltip" title="${phrase('Move Up')}"><i class="mdi mdi-arrow-up-circle mdi-18px"></i></button>` : '';
      const downBtn =
        index < items.length - 1 ? `<button type="button" class="btn btn-sm btn-link text-secondary pb-item-down p-0" data-bs-toggle="tooltip" title="${phrase('Move Down')}"><i class="mdi mdi-arrow-down-circle mdi-18px"></i></button>` : '';
      const delBtn = `<button type="button" class="btn btn-sm btn-link text-danger pb-item-del p-0 ms-1" data-bs-toggle="tooltip" title="${phrase('Remove')}"><i class="mdi mdi-close-circle mdi-18px"></i></button>`;

      html += `
        <div class="pb-item-row" data-idx="${index}">
          <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <span class="small fw-bold text-primary">${itemLabel} #${index + 1}</span>
            <div class="d-flex gap-1">
              ${upBtn}
              ${downBtn}
              ${delBtn}
            </div>
          </div>
      `;

      for (const key in item) {
        const fieldLabel = phrase(key.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase()));
        const itemId = `pb_item_${this.selectedId || 'prop'}_${index}_${key}`;
        html += `<div class="mb-3"><label for="${itemId}" class="small text-muted d-block mb-1">${fieldLabel}</label>`;

        if (key === 'src' || key === 'image') {
          const uploaderUrl = this.options.uploaderUrl;
          const val = String(item[key] || '').replace(/"/g, '&quot;');
          const placeholder = phrase(key.charAt(0).toUpperCase() + key.slice(1) + ' URL');
          const inputClass = `${this.classes.input || 'form-control form-control-sm'} pb-item-input`;
          html += `
            <div class="pb-prop-img-input input-group input-group-sm">
              <input type="text" id="${itemId}" name="${itemId}" data-key="${key}" class="${inputClass}" value="${val}" placeholder="${placeholder}">
              <a href="${uploaderUrl}" class="btn btn-sm btn-outline-secondary pb-browse-img --modal" data-bs-toggle="tooltip" title="${phrase('Browse')}">
                <i class="mdi mdi-folder-open"></i>
              </a>
            </div>
          `;
        } else if (key === 'body' || key === 'content' || key === 'subtitle' || key === 'bio' || key === 'quote') {
          const content = this.markdownToHtml(item[key]) || '';
          const wysiwygClass = `${this.classes.wysiwyg || 'pb-wysiwyg'}`;
          const wysiwygEditorClass = `${this.classes.wysiwygEditor || 'pb-wysiwyg-editor'}`;
          html += `
            <div class="${wysiwygClass} mt-1" data-key="${key}" data-item-idx="${index}">
              <div class="pb-wysiwyg-toolbar">
                <button type="button" data-cmd="bold" data-bs-toggle="tooltip" title="${phrase('Bold')}"><i class="mdi mdi-format-bold"></i></button>
                <button type="button" data-cmd="italic" data-bs-toggle="tooltip" title="${phrase('Italic')}"><i class="mdi mdi-format-italic"></i></button>
                <button type="button" data-cmd="underline" data-bs-toggle="tooltip" title="${phrase('Underline')}"><i class="mdi mdi-format-underline"></i></button>
                <button type="button" data-cmd="strikeThrough" data-bs-toggle="tooltip" title="${phrase('Strikethrough')}"><i class="mdi mdi-format-strikethrough"></i></button>
                <span class="pb-wys-sep"></span>
                <button type="button" data-cmd="createLink" data-bs-toggle="tooltip" title="${phrase('Insert Link')}"><i class="mdi mdi-link-variant"></i></button>
                <button type="button" data-cmd="unlink" data-bs-toggle="tooltip" title="${phrase('Remove Link')}"><i class="mdi mdi-link-variant-off"></i></button>
                <span class="pb-wys-sep"></span>
                <button type="button" data-cmd="insertUnorderedList" data-bs-toggle="tooltip" title="${phrase('Bullet List')}"><i class="mdi mdi-format-list-bulleted"></i></button>
                <button type="button" data-cmd="insertOrderedList" data-bs-toggle="tooltip" title="${phrase('Numbered List')}"><i class="mdi mdi-format-list-numbered"></i></button>
                <span class="pb-wys-sep"></span>
                <button type="button" data-cmd="removeFormat" data-bs-toggle="tooltip" title="${phrase('Clear Formatting')}"><i class="mdi mdi-format-clear"></i></button>
              </div>
              <div id="${itemId}" class="${wysiwygEditorClass} bg-body text-body" contenteditable="true" style="min-height:120px;max-height:300px;overflow-y:auto;padding:12px 14px;font-size:14px;line-height:1.4;outline:none">${content}</div>
            </div>
          `;
        } else {
          const val = String(item[key] || '').replace(/"/g, '&quot;');
          const inputClass = `${this.classes.input || 'form-control form-control-sm'} pb-item-input`;
          html += `<input type="text" id="${itemId}" name="${itemId}" data-key="${key}" class="${inputClass}" value="${val}">`;
        }
        html += '</div>';
      }
      html += '</div>';
    });

    html += `
      <button type="button" class="btn btn-sm btn-outline-primary w-100 pb-item-add border-dashed py-2">
        <i class="mdi mdi-plus-circle-outline me-1"></i>${phrase('Add')} ${itemLabel}
      </button>
    </div>`;
    return html;
  };

  /**
   * Bind events to Items Manager
   */
  Prototype.bindItemsManager = function (component) {
    if (!component.props) component.props = {};
    const self = this;
    const definition = this.componentDefinitions[component.type] || {};

    this.propertiesElement.querySelectorAll('.pb-item-row').forEach((row) => {
      const index = parseInt(row.dataset.idx);

      row.querySelectorAll('.pb-item-input').forEach((input) => {
        input.addEventListener('input', () => {
          if (!component.props.items) component.props.items = JSON.parse(JSON.stringify(definition.defaults.items));
          component.props.items[index][input.dataset.key] = input.value;
          self.saveToHistory();
          clearTimeout(self.debounceTimer);
          self.debounceTimer = setTimeout(() => {
            self.render();
            self.syncToInput();
          }, 150);
        });
      });

      const upBtn = row.querySelector('.pb-item-up');
      if (upBtn) {
        upBtn.onclick = () => {
          const items = component.props.items || JSON.parse(JSON.stringify(definition.defaults.items));
          const temp = items[index];
          items[index] = items[index - 1];
          items[index - 1] = temp;
          component.props.items = items;
          self.saveToHistory();
          self.render();
          self.renderProperties();
          self.syncToInput();
        };
      }

      const downBtn = row.querySelector('.pb-item-down');
      if (downBtn) {
        downBtn.onclick = () => {
          const items = component.props.items || JSON.parse(JSON.stringify(definition.defaults.items));
          const temp = items[index];
          items[index] = items[index + 1];
          items[index + 1] = temp;
          component.props.items = items;
          self.saveToHistory();
          self.render();
          self.renderProperties();
          self.syncToInput();
        };
      }

      const delBtn = row.querySelector('.pb-item-del');
      if (delBtn) {
        delBtn.onclick = () => {
          const items = component.props.items || JSON.parse(JSON.stringify(definition.defaults.items));
          items.splice(index, 1);
          component.props.items = items;
          self.saveToHistory();
          self.render();
          self.renderProperties();
          self.syncToInput();
        };
      }
    });

    const addBtn = this.propertiesElement.querySelector('.pb-item-add');
    if (addBtn) {
      addBtn.onclick = () => {
        if (!component.props.items) component.props.items = JSON.parse(JSON.stringify(definition.defaults.items));
        const items = component.props.items;
        const lastItem = items[items.length - 1] || definition.defaults.items[0];
        const newItem = JSON.parse(JSON.stringify(lastItem));
        for (const key in newItem) newItem[key] = '';
        items.push(newItem);
        self.saveToHistory();
        self.render();
        self.renderProperties();
        self.syncToInput();
      };
    }
  };

  /**
   * Open the Page Settings modal
   * @param {string} sourceSelector Selector for the hidden metadata fields
   */
  Prototype.openSettings = function (sourceSelector) {
    const modalId = 'pb-settings-modal';
    const source = document.querySelector(sourceSelector);
    if (!source) return;

    let modal = document.getElementById(modalId);
    if (modal) {
      $(modal).modal('dispose');
      modal.remove();
    }

    modal = document.createElement('div');
    modal.id = modalId;
    modal.className = 'modal fade';
    modal.setAttribute('tabindex', '-1');
    modal.innerHTML = `
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">${phrase('Page Settings')}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="${phrase('Close')}"></button>
          </div>
          <div class="modal-body p-4"></div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-primary w-100 rounded-pill" data-bs-dismiss="modal">${phrase('Done')}</button>
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

    const body = modal.querySelector('.modal-body');

    while (source.firstChild) body.appendChild(source.firstChild);

    const $modal = $(modal);
    $modal.modal('show');

    $modal.on('hidden.bs.modal', () => {
      while (body.firstChild) source.appendChild(body.firstChild);

      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
      $modal.modal('dispose');
      modal.remove();
    });
  };

  window.AksaraPageBuilder = AksaraPageBuilder;
})();
