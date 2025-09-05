@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <!-- Заголовок -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <div>
                    <h2 class="display-6 fw-bold text-dark mb-2">Мои файлы</h2>
                    <p class="text-muted">Управление вашими файлами</p>
                </div>
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
                    <div class="text-muted">
                        <small>Всего файлов: {{ $files->total() }}</small>
                    </div>
                    <button id="uploadBtn" class="btn btn-primary">
                        <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Добавить файлы</span>
                    </button>
                </div>
            </div>

            <!-- Upload Form (Hidden) -->
            <div id="uploadForm" class="card shadow-sm border-0 mb-4" style="display: none;">
                <div class="card-body">
                    <h5 class="card-title mb-3">Загрузка файлов</h5>
                    <form id="fileUploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Выберите файлы</label>
                            <input type="file" id="fileInput" name="files[]" multiple accept="image/*,.pdf,.doc,.docx,.txt" class="form-control">
                            <div class="form-text">Максимум 10 файлов, до 10MB каждый</div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_public" class="form-check-input" id="isPublic">
                                <label class="form-check-label" for="isPublic">
                                    Сделать файлы публичными
                                </label>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload"></i> Загрузить
                            </button>
                            <button type="button" id="cancelUpload" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Отмена
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Progress Bar -->
            <div id="progressContainer" class="card shadow-sm border-0 mb-4" style="display: none;">
                <div class="card-body">
                    <div class="progress mb-2">
                        <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <p id="progressText" class="text-muted mb-0">Загрузка...</p>
                </div>
            </div>

            <!-- Список файлов -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    @if($files->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="table-responsive d-none d-lg-block">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <div class="form-check">
                                                <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                            </div>
                                        </th>
                                        <th width="60">Превью</th>
                                        <th>Имя файла</th>
                                        <th>Размер</th>
                                        <th>Тип</th>
                                        <th>Дата</th>
                                        <th>Статус</th>
                                        <th width="200">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $file)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input file-checkbox" value="{{ $file->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                @if(\App\Helpers\FileHelper::isImage($file->mime_type))
                                                    <x-image-preview :src="route('img.show', $file->id)" :alt="$file->original_name" size="40px" />
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <span class="text-muted small fw-bold">{{ strtoupper($file->extension) }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $file->original_name }}</div>
                                                <small class="text-muted">ID: {{ $file->id }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ \App\Helpers\FileHelper::formatSize($file->size) }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $file->mime_type }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $file->created_at->format('d.m.Y H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($file->is_public)
                                                    <span class="badge bg-success">Публичный</span>
                                                @else
                                                    <span class="badge bg-warning">Приватный</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('user.files.download', $file) }}" class="btn btn-outline-primary" title="Скачать">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($file->is_public)
                                                        <button onclick="copyPublicUrl('{{ $file->public_url }}')" class="btn btn-outline-success" title="Копировать ссылку">
                                                            <i class="fas fa-link"></i>
                                                        </button>
                                                    @endif
                                                    <button onclick="togglePublic({{ $file->id }})" class="btn btn-outline-info" title="{{ $file->is_public ? 'Сделать приватным' : 'Сделать публичным' }}">
                                                        <i class="fas fa-{{ $file->is_public ? 'lock' : 'unlock' }}"></i>
                                                    </button>
                                                    <button onclick="deleteFile({{ $file->id }})" class="btn btn-outline-danger" title="Удалить">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-lg-none">
                            @foreach($files as $file)
                                <div class="border-bottom p-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input file-checkbox" value="{{ $file->id }}">
                                        </div>
                                        
                                        <div class="flex-shrink-0">
                                            @if(\App\Helpers\FileHelper::isImage($file->mime_type))
                                                <x-image-preview :src="route('img.show', $file->id)" :alt="$file->original_name" size="50px" />
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <span class="text-muted small fw-bold">{{ strtoupper($file->extension) }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fw-bold text-truncate">{{ $file->original_name }}</div>
                                            <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                                                <span class="badge bg-secondary">{{ \App\Helpers\FileHelper::formatSize($file->size) }}</span>
                                                @if($file->is_public)
                                                    <span class="badge bg-success">Публичный</span>
                                                @else
                                                    <span class="badge bg-warning">Приватный</span>
                                                @endif
                                            </div>
                                            <small class="text-muted d-block mt-1">{{ $file->created_at->format('d.m.Y H:i') }}</small>
                                        </div>
                                    </div>

                                    <!-- Кнопки внизу карточки, выровненные справа -->
                                    <div class="d-flex flex-wrap gap-1 mt-3 justify-content-end">
                                        <a href="{{ route('user.files.download', $file) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Скачать</span>
                                        </a>
                                        @if($file->is_public)
                                            <button onclick="copyPublicUrl('{{ $file->public_url }}')" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-link"></i> <span class="d-none d-sm-inline">Ссылка</span>
                                            </button>
                                        @endif
                                        <button onclick="togglePublic({{ $file->id }})" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-{{ $file->is_public ? 'lock' : 'unlock' }}"></i>
                                        </button>
                                        <button onclick="deleteFile({{ $file->id }})" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Панель действий -->
                        <div class="card-footer bg-light">
                            <div class="d-flex flex-column gap-3">
                                <!-- Мобильная версия - большие кнопки друг под другом -->
                                <div class="d-lg-none">
                                    <button id="selectAllBtn" class="btn btn-lg btn-outline-primary w-100">
                                        <i class="fas fa-check-square"></i> Выбрать все
                                    </button>
                                    <button id="downloadSelectedBtn" class="btn btn-lg btn-primary w-100 mt-2" disabled>
                                        <i class="fas fa-download"></i> Скачать выбранные (<span id="selectedCount">0</span>)
                                    </button>
                                </div>
                                
                                <!-- Десктопная версия -->
                                <div class="d-none d-lg-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-2">
                                        <button id="selectAllBtnDesktop" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-check-square"></i> Выбрать все
                                        </button>
                                        <button id="downloadSelectedBtnDesktop" class="btn btn-sm btn-primary" disabled>
                                            <i class="fas fa-download"></i> Скачать выбранные (<span id="selectedCountDesktop">0</span>)
                                        </button>
                                    </div>
                                    <div class="text-muted">
                                        <small>Выбрано файлов: <span id="selectedFilesCountDesktop">0</span></small>
                                    </div>
                                </div>
                                
                                <!-- Счетчик для мобильной версии -->
                                <div class="d-lg-none text-center text-muted">
                                    <small>Выбрано файлов: <span id="selectedFilesCount">0</span></small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Нет файлов</h5>
                            <p class="text-muted">Загрузите свой первый файл, нажав кнопку "Добавить файлы"</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Пагинация -->
            @if($files->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $files->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Upload functionality
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadForm = document.getElementById('uploadForm');
    const cancelUpload = document.getElementById('cancelUpload');
    const fileUploadForm = document.getElementById('fileUploadForm');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const downloadSelectedBtn = document.getElementById('downloadSelectedBtn');
    const selectAllBtnDesktop = document.getElementById('selectAllBtnDesktop');
    const downloadSelectedBtnDesktop = document.getElementById('downloadSelectedBtnDesktop');
    
    // File selection
    let selectedFiles = new Set();
    
    // Upload functionality
    if (uploadBtn && uploadForm) {
        uploadBtn.addEventListener('click', function() {
            uploadForm.style.display = 'block';
        });
    }
    
    if (cancelUpload && uploadForm) {
        cancelUpload.addEventListener('click', function() {
            uploadForm.style.display = 'none';
            const fileInput = document.getElementById('fileInput');
            if (fileInput) fileInput.value = '';
        });
    }
    
    // File upload with progress
    if (fileUploadForm) {
        fileUploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            
            if (progressContainer) progressContainer.style.display = 'block';
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const token = csrfToken ? csrfToken.getAttribute('content') : '';
            
            fetch('{{ route("user.files.upload") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => response.json())
            .then(data => {
                if (progressContainer) progressContainer.style.display = 'none';
                
                if (data.success) {
                    location.reload();
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                if (progressContainer) progressContainer.style.display = 'none';
                alert('Ошибка загрузки: ' + error.message);
            });
        });
    }
    
    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.file-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                if (this.checked) {
                    selectedFiles.add(cb.value);
                } else {
                    selectedFiles.delete(cb.value);
                }
            });
            updateDownloadButton();
        });
    }
    
    // Функция для выбора всех файлов
    function selectAllFiles() {
        const checkboxes = document.querySelectorAll('.file-checkbox');
        const allSelected = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(cb => {
            cb.checked = !allSelected;
            if (cb.checked) {
                selectedFiles.add(cb.value);
            } else {
                selectedFiles.delete(cb.value);
            }
        });
        
        if (selectAllCheckbox) selectAllCheckbox.checked = !allSelected;
        updateDownloadButton();
    }

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', selectAllFiles);
    }
    
    if (selectAllBtnDesktop) {
        selectAllBtnDesktop.addEventListener('click', selectAllFiles);
    }
    
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('file-checkbox')) {
            if (e.target.checked) {
                selectedFiles.add(e.target.value);
            } else {
                selectedFiles.delete(e.target.value);
            }
            updateDownloadButton();
            
            // Update select all checkbox
            const checkboxes = document.querySelectorAll('.file-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const noneChecked = Array.from(checkboxes).every(cb => !cb.checked);
            
            if (selectAllCheckbox) {
                if (allChecked) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else if (noneChecked) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }
        }
    });
    
    function updateDownloadButton() {
        // Мобильные кнопки
        const btn = document.getElementById('downloadSelectedBtn');
        const countSpan = document.getElementById('selectedCount');
        const filesCountSpan = document.getElementById('selectedFilesCount');
        
        // Десктопные кнопки
        const btnDesktop = document.getElementById('downloadSelectedBtnDesktop');
        const countSpanDesktop = document.getElementById('selectedCountDesktop');
        const filesCountSpanDesktop = document.getElementById('selectedFilesCountDesktop');
        
        if (btn) {
            btn.disabled = selectedFiles.size === 0;
        }
        if (btnDesktop) {
            btnDesktop.disabled = selectedFiles.size === 0;
        }
        
        if (countSpan) countSpan.textContent = selectedFiles.size;
        if (countSpanDesktop) countSpanDesktop.textContent = selectedFiles.size;
        if (filesCountSpan) filesCountSpan.textContent = selectedFiles.size;
        if (filesCountSpanDesktop) filesCountSpanDesktop.textContent = selectedFiles.size;
    }
    
    // Функция для скачивания выбранных файлов
    function downloadSelectedFiles() {
        if (selectedFiles.size === 0) return;
        
        selectedFiles.forEach(fileId => {
            window.open(`/files/${fileId}/download`, '_blank');
        });
    }

    // Download selected files
    if (downloadSelectedBtn) {
        downloadSelectedBtn.addEventListener('click', downloadSelectedFiles);
    }
    
    if (downloadSelectedBtnDesktop) {
        downloadSelectedBtnDesktop.addEventListener('click', downloadSelectedFiles);
    }
    
    // Utility functions
    window.copyPublicUrl = function(url) {
        navigator.clipboard.writeText(url).then(() => {
            // Bootstrap toast notification
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i>Ссылка скопирована в буфер обмена
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 3000);
        });
    };
    
    window.togglePublic = function(fileId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const token = csrfToken ? csrfToken.getAttribute('content') : '';
        
        fetch(`/files/${fileId}/toggle-public`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        });
    };
    
    window.deleteFile = function(fileId) {
        if (!confirm('Вы уверены, что хотите удалить этот файл?')) return;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const token = csrfToken ? csrfToken.getAttribute('content') : '';
        
        fetch(`/files/${fileId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        });
    };
});
</script>

@endsection
