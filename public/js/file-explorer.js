document.addEventListener('DOMContentLoaded', function() {
    // === DRAG & DROP UPLOAD ===
    const page = document.querySelector('.fi-page');
    const dropzone = document.getElementById('fe-dropzone');
    let dragCounter = 0;

    if (page && dropzone) {
        page.addEventListener('dragenter', function(e) {
            e.preventDefault();
            dragCounter++;
            if (e.dataTransfer.types.includes('Files')) {
                dropzone.style.display = 'block';
            }
        });

        page.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dragCounter--;
            if (dragCounter <= 0) {
                dropzone.style.display = 'none';
                dragCounter = 0;
            }
        });

        page.addEventListener('dragover', function(e) {
            e.preventDefault();
        });

        page.addEventListener('drop', function(e) {
            e.preventDefault();
            dragCounter = 0;
            dropzone.style.display = 'none';

            if (e.dataTransfer.files.length > 0) {
                var fileInput = page.querySelector('input[type="file"]');
                if (fileInput) {
                    var dt = new DataTransfer();
                    for (var i = 0; i < e.dataTransfer.files.length; i++) {
                        dt.items.add(e.dataTransfer.files[i]);
                    }
                    fileInput.files = dt.files;
                    fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    }

    // === CONTEXT MENU ===
    var menu = document.getElementById('fe-context-menu');
    if (!menu) {
        menu = document.createElement('div');
        menu.id = 'fe-context-menu';
        menu.style.cssText = 'display:none;position:fixed;z-index:9999;background:#1e1e2e;border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:4px;min-width:180px;box-shadow:0 8px 30px rgba(0,0,0,0.5);';
        document.body.appendChild(menu);
    }

    function showContextMenu(e, path, name, isDir, folder) {
        e.preventDefault();
        e.stopPropagation();

        var items = '';
        var wireEl = document.querySelector('[wire\\:id]');
        var wireId = wireEl ? wireEl.getAttribute('wire:id') : null;
        var wire = wireId ? Livewire.find(wireId) : null;
        if (!wire) return;

        if (!isDir) {
            items += '<div class="fe-ctx-item" data-action="preview">Preview</div>';
        } else {
            items += '<div class="fe-ctx-item" data-action="open">Open</div>';
        }

        if (folder) {
            items += '<div class="fe-ctx-item" data-action="openfolder">Open folder location</div>';
        }

        items += '<div class="fe-ctx-sep"></div>';
        items += '<div class="fe-ctx-item" data-action="rename">Rename</div>';
        if (!isDir) {
            items += '<div class="fe-ctx-item" data-action="copy">Duplicate</div>';
        }
        items += '<div class="fe-ctx-sep"></div>';
        items += '<div class="fe-ctx-item fe-ctx-danger" data-action="delete">Delete</div>';

        menu.innerHTML = items;
        menu.style.display = 'block';

        var x = Math.min(e.clientX, window.innerWidth - 200);
        var y = Math.min(e.clientY, window.innerHeight - 250);
        menu.style.left = x + 'px';
        menu.style.top = y + 'px';

        menu.querySelectorAll('[data-action]').forEach(function(el) {
            el.addEventListener('click', function() {
                menu.style.display = 'none';
                switch(el.dataset.action) {
                    case 'preview': wire.call('viewFile', path); break;
                    case 'open': wire.call('navigateTo', path); break;
                    case 'openfolder':
                        wire.call('clearSearch').then(function() {
                            wire.call('navigateTo', folder === '/' ? '' : folder);
                        });
                        break;
                    case 'rename': wire.call('startRename', path); break;
                    case 'copy': wire.call('copyFile', path); break;
                    case 'delete':
                        if (confirm('Delete ' + name + '?')) wire.call('deleteFile', path);
                        break;
                }
            });
        });
    }

    document.addEventListener('click', function() {
        if (menu) menu.style.display = 'none';
    });

    document.addEventListener('contextmenu', function(e) {
        var row = e.target.closest('[data-ctx-path]');
        if (row) {
            showContextMenu(e, row.dataset.ctxPath, row.dataset.ctxName, row.dataset.ctxDir === '1', row.dataset.ctxFolder || null);
        } else if (menu) {
            menu.style.display = 'none';
        }
    });

    // === DRAG TO MOVE FILES ===
    document.addEventListener('dragstart', function(e) {
        var row = e.target.closest('[data-drag-path]');
        if (row) {
            e.dataTransfer.setData('text/plain', row.dataset.dragPath);
            e.dataTransfer.effectAllowed = 'move';
            row.style.opacity = '0.4';
        }
    });

    document.addEventListener('dragend', function(e) {
        var row = e.target.closest('[data-drag-path]');
        if (row) row.style.opacity = '1';
    });

    document.addEventListener('dragover', function(e) {
        var row = e.target.closest('[data-drop-folder]');
        if (row) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            row.style.background = 'rgba(99,102,241,0.15)';
        }
    });

    document.addEventListener('dragleave', function(e) {
        var row = e.target.closest('[data-drop-folder]');
        if (row) row.style.background = '';
    });

    document.addEventListener('drop', function(e) {
        var row = e.target.closest('[data-drop-folder]');
        if (row) {
            e.preventDefault();
            var sourcePath = e.dataTransfer.getData('text/plain');
            var targetFolder = row.dataset.dropFolder;
            row.style.background = '';
            if (sourcePath && targetFolder && sourcePath !== targetFolder) {
                var w = document.querySelector('[wire\\:id]');
                if (w) Livewire.find(w.getAttribute('wire:id')).call('moveFile', sourcePath, targetFolder);
            }
        }
    });
});
