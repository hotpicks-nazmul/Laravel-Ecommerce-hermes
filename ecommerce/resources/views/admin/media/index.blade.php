@extends('admin.layouts.app')

@section('title', 'Media Library')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-images me-2"></i>Media Library</h4>
</div>

<!-- Filter and Search Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.media.index') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Filter by Type</label>
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" {{ request('type', 'all') == 'all' ? 'selected' : '' }}>All Files</option>
                    <option value="images" {{ request('type') == 'images' ? 'selected' : '' }}>Images</option>
                    <option value="videos" {{ request('type') == 'videos' ? 'selected' : '' }}>Videos</option>
                    <option value="documents" {{ request('type') == 'documents' ? 'selected' : '' }}>Documents</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Search Files</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search by filename..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search') || request('type'))
                    <a href="{{ route('admin.media.index') }}" class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-x-circle"></i>
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Per Page</label>
                <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25 per page</option>
                    <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100 per page</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Showing {{ $paginator->total() }} total files
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Upload Area -->
<div class="upload-area mb-4" id="uploadArea">
    <form id="uploadForm" enctype="multipart/form-data">
        @csrf
        <input type="file" name="files[]" id="fileInput" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" style="display: none;">
        <div class="d-flex align-items-center justify-content-center gap-3">
            <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                <i class="bi bi-cloud-upload me-2"></i> Select Files
            </button>
            <span class="text-muted">or drag and drop files here</span>
        </div>
        <div class="mt-2 text-muted small">
            Max file size: 10MB. Supported: Images, Videos, Audio, Documents
        </div>
    </form>
    <div id="uploadProgress" class="mt-3" style="display: none;">
        <div class="progress" style="height: 8px;">
            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
        <small class="text-muted mt-1 d-block" id="uploadProgressText">Uploading...</small>
    </div>
</div>

<!-- Media Grid -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($paginator->count() > 0)
        <div class="media-grid p-3" id="mediaGrid">
            @foreach($paginator as $file)
            <div class="media-item" data-path="{{ $file['path'] }}" onclick="viewFile('{{ $file['path'] }}')">
                <input type="checkbox" class="form-check-input checkbox" 
                       onclick="event.stopPropagation();">
                
                <div class="actions">
                    <button class="btn btn-sm btn-outline-primary" title="View" onclick="event.stopPropagation(); viewFile('{{ $file['path'] }}')">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" title="Copy URL" onclick="event.stopPropagation(); copyUrl('{{ $file['url'] }}')">
                        <i class="bi bi-clipboard"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="event.stopPropagation(); deleteFile('{{ $file['path'] }}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                
                <div class="preview">
                    @if($file['is_image'])
                    <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}">
                    @else
                    <div class="file-icon">
                        <i class="bi bi-file-earmark"></i>
                    </div>
                    @endif
                </div>
                
                <div class="info">
                    <div class="name" title="{{ $file['name'] }}">{{ $file['name'] }}</div>
                    <div class="size">{{ $file['size'] > 1024 * 1024 ? number_format($file['size'] / 1024 / 1024, 2) . ' MB' : number_format($file['size'] / 1024, 2) . ' KB' }}</div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($paginator->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                <div class="text-muted small">
                    Showing {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} of {{ $paginator->total() }} files
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if ($paginator->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">‹</span>
                        </li>
                        @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
                        </li>
                        @endif

                        {{-- Numbered Links --}}
                        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                            @if ($i >= $paginator->currentPage() - 2 && $i <= $paginator->currentPage() + 2)
                                @if ($i == $paginator->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                @else
                                <li class="page-item"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                                @endif
                            @endif
                        @endfor

                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
                        </li>
                        @else
                        <li class="page-item disabled">
                            <span class="page-link">›</span>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-5">
            <i class="bi bi-images text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3">No Media Files</h5>
            <p class="text-muted">Upload your first file using the form above.</p>
        </div>
        @endif
    </div>
</div>

<!-- File Details Modal -->
<div class="modal fade" id="fileDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark me-2"></i>File Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <div id="imagePreviewContainer">
                            <img src="" alt="" class="img-fluid rounded" id="modalPreview" style="max-height: 350px; width: 100%; object-fit: contain; background: #f8f9fa;">
                        </div>
                        <div id="filePreviewContainer" class="text-center py-5" style="display: none;">
                            <i class="bi bi-file-earmark-text" style="font-size: 5rem; color: #6c757d;"></i>
                            <p class="mt-2 text-muted" id="modalFileType"></p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Filename</label>
                            <div class="fw-medium" id="modalName"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">File Size</label>
                            <div id="modalSize"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">File Type</label>
                            <div><span class="badge bg-secondary" id="modalType"></span></div>
                        </div>
                        <div class="mb-3" id="dimensionsContainer" style="display: none;">
                            <label class="form-label text-muted small mb-1">Dimensions</label>
                            <div id="modalDimensions"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">URL</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="modalUrl" readonly>
                                <button class="btn btn-outline-primary" onclick="copyUrlFromModal()">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-outline-danger" onclick="deleteFileFromModal()">
                                <i class="bi bi-trash me-2"></i>Delete File
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this file? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Success</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="successMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
    }
    
    .media-item {
        position: relative;
        border: 2px solid transparent;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
        cursor: pointer;
        background: #fff;
    }
    
    .media-item:hover {
        border-color: #0d6efd;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .media-item .checkbox {
        position: absolute;
        top: 8px;
        left: 8px;
        z-index: 10;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .media-item:hover .checkbox,
    .media-item.selected .checkbox {
        opacity: 1;
    }
    
    .media-item.selected .checkbox {
        opacity: 1;
    }
    
    .media-item .preview {
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        overflow: hidden;
    }
    
    .media-item .preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    
    .media-item .preview .file-icon {
        font-size: 3rem;
        color: #6c757d;
    }
    
    .media-item .info {
        padding: 10px;
        border-top: 1px solid #eee;
    }
    
    .media-item .info .name {
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .media-item .info .size {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .media-item .actions {
        position: absolute;
        top: 8px;
        right: 8px;
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .media-item:hover .actions {
        opacity: 1;
    }
    
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .upload-area:hover,
    .upload-area.dragover {
        border-color: #0d6efd;
        background-color: #e7f1ff;
    }
    
    /* Custom Pagination Styles - Compact */
    .pagination {
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 2px;
    }
    
    .pagination .page-item .page-link {
        border-radius: 4px;
        border: 1px solid #dee2e6;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        min-width: 32px;
        text-align: center;
        color: #495057;
        background-color: #fff;
    }
    
    .pagination .page-item .page-link i {
        font-size: 0.7rem;
    }
    
    .pagination .page-item .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    /* Fix filename overflow in modal */
    #fileDetailsModal .fw-medium {
        word-wrap: break-word;
        word-break: break-all;
        overflow-wrap: break-word;
        max-width: 100%;
    }
    
    /* Custom Modal Styles */
    #fileDetailsModal .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    #fileDetailsModal .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
    
    #fileDetailsModal .modal-body {
        padding: 1.5rem;
    }
    
    #fileDetailsModal #modalPreview {
        border-radius: 8px;
        background-color: #f8f9fa;
        max-height: 400px;
        object-fit: contain;
    }
    
    #fileDetailsModal .table td {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    #fileDetailsModal .table td:first-child {
        width: 80px;
        font-weight: 500;
        color: #6c757d;
    }
    
    /* Non-image file preview in modal */
    #fileDetailsModal .file-preview-icon {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    #fileDetailsModal .file-preview-icon i {
        font-size: 5rem;
        color: #6c757d;
    }
    
    /* ============================================
       CONSISTENT TABLE STYLES
       ============================================ */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: #495057;
        padding: 0.75rem 0.5rem;
        white-space: nowrap;
    }
    
    .table tbody tr {
        transition: background-color 0.15s ease-in-out;
    }
    
    .table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.04);
    }
    
    .table td {
        vertical-align: middle;
        padding: 0.75rem 0.5rem;
    }
    
    /* ============================================
       CONSISTENT PAGINATION STYLES
       ============================================ */
    .card-footer {
        border-top: 1px solid #dee2e6;
    }
    
    .pagination {
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 2px;
    }
    
    .pagination .page-item .page-link {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        color: #495057;
        background-color: #fff;
        transition: all 0.15s ease;
    }
    
    .pagination .page-item .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #0d6efd;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
    
    /* ============================================
       CONSISTENT CARD STYLES
       ============================================ */
    .card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: box-shadow 0.2s ease;
    }
    
    .card:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.25rem;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 1rem 1.25rem;
    }
    
    /* ============================================
       STAT CARD ROW STYLES (Available for use)
       ============================================ */
    .stat-card-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    
    .stat-card-row .stat-card {
        min-height: 80px;
        align-items: stretch;
    }
    
    .stat-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .stat-card .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .stat-card-primary .stat-card-icon { background: #e8f4fd; color: #0d6efd; }
    .stat-card-success .stat-card-icon { background: #d1e7dd; color: #198754; }
    .stat-card-info .stat-card-icon { background: #cff4fc; color: #0dcaf0; }
    .stat-card-warning .stat-card-icon { background: #fff3cd; color: #ffc107; }
    .stat-card-danger .stat-card-icon { background: #f8d7da; color: #dc3545; }
    .stat-card-secondary .stat-card-icon { background: #e2e3e5; color: #6c757d; }
    
    .stat-card-content {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    
    .stat-card-label {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 2px;
    }
    
    .stat-card-value {
        font-size: 24px;
        font-weight: 700;
        color: #212529;
    }
    
    @media (max-width: 992px) {
        .stat-card-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 576px) {
        .stat-card-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
let currentDeletePath = '';

// Drag and drop
const uploadArea = document.getElementById('uploadArea');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        uploadFiles(files);
    }
});

document.getElementById('fileInput').addEventListener('change', function(e) {
    if (this.files.length > 0) {
        uploadFiles(this.files);
    }
});

// Upload files with real progress
function uploadFiles(files) {
    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    
    const progressDiv = document.getElementById('uploadProgress');
    const progressBar = progressDiv.querySelector('.progress-bar');
    const progressText = document.getElementById('uploadProgressText');
    
    progressDiv.style.display = 'block';
    progressBar.style.width = '0%';
    progressText.textContent = 'Uploading 0%';
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("admin.media.upload") }}', true);
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = 'Uploading ' + percent + '%';
        }
    });
    
    xhr.onload = function() {
        progressDiv.style.display = 'none';
        
        try {
            const data = JSON.parse(xhr.responseText);
            if (data.success) {
                showSuccessModal(data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showSuccessModal(data.message || 'Upload failed');
            }
        } catch (e) {
            showSuccessModal('Upload failed');
        }
    };
    
    xhr.onerror = function() {
        progressDiv.style.display = 'none';
        showSuccessModal('Upload failed: Network error');
    };
    
    xhr.send(formData);
}

// Show success modal (instead of alert)
function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
}

// View file details
function viewFile(path) {
    fetch('{{ route("admin.media.show") }}?path=' + encodeURIComponent(path))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const file = data.file;
                
                document.getElementById('modalName').textContent = file.name;
                document.getElementById('modalSize').textContent = file.size_formatted;
                document.getElementById('modalType').textContent = file.extension.toUpperCase();
                document.getElementById('modalUrl').value = file.url;
                
                // Show dimensions for images
                const dimensionsContainer = document.getElementById('dimensionsContainer');
                if (file.dimensions) {
                    dimensionsContainer.style.display = 'block';
                    document.getElementById('modalDimensions').textContent = file.dimensions;
                } else {
                    dimensionsContainer.style.display = 'none';
                }
                
                // Show/hide image preview based on file type
                const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                const isImage = imageExtensions.includes(file.extension.toLowerCase());
                
                const imageContainer = document.getElementById('imagePreviewContainer');
                const fileContainer = document.getElementById('filePreviewContainer');
                
                if (isImage) {
                    imageContainer.style.display = 'block';
                    fileContainer.style.display = 'none';
                    document.getElementById('modalPreview').src = file.url;
                } else {
                    imageContainer.style.display = 'none';
                    fileContainer.style.display = 'block';
                    document.getElementById('modalFileType').textContent = file.extension.toUpperCase() + ' File';
                }
                
                currentDeletePath = file.path;
                
                const modal = new bootstrap.Modal(document.getElementById('fileDetailsModal'));
                modal.show();
            }
        });
}

// Copy URL
function copyUrl(url) {
    navigator.clipboard.writeText(window.location.origin + url).then(() => {
        showSuccessModal('URL copied to clipboard');
    });
}

function copyUrlFromModal() {
    const url = document.getElementById('modalUrl').value;
    copyUrl(url);
}

// Delete file
function deleteFile(path) {
    currentDeletePath = path;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function deleteFileFromModal() {
    bootstrap.Modal.getInstance(document.getElementById('fileDetailsModal')).hide();
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (currentDeletePath) {
        fetch('{{ route("admin.media.destroy") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ path: currentDeletePath })
        })
        .then(response => response.json())
        .then(data => {
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            
            if (data.success) {
                showSuccessModal(data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showSuccessModal(data.message || 'Delete failed');
            }
        });
    }
});
</script>
@endpush
