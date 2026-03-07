@extends('admin.layouts.app')

@section('title', 'Bulk SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Bulk SMS</h4>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Customers with Phone</div>
                <div class="h4 mb-0 text-primary">{{ $stats['total_customers'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Active Subscribers</div>
                <div class="h4 mb-0 text-success">{{ $stats['total_subscribers'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Sent</div>
                <div class="h4 mb-0 text-info">{{ $stats['total_sent'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Successful</div>
                <div class="h4 mb-0 text-success">{{ $stats['successful'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Send SMS Form -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-send me-2"></i>Send New SMS</h6>
            </div>
            <div class="card-body">
                <form id="smsForm" method="POST" action="{{ route('admin.marketing.bulk-sms.send') }}">
                    @csrf
                    
                    <!-- Recipients Type -->
                    <div class="mb-3">
                        <label class="form-label">Recipients <span class="text-danger">*</span></label>
                        <select name="recipients_type" id="recipientsType" class="form-select" onchange="updateRecipientCount()">
                            <option value="">Select recipients...</option>
                            <option value="all_customers">All Customers (with phone)</option>
                            <option value="subscribers">SMS Subscribers</option>
                            <option value="registered_users">Registered Users Only</option>
                            <option value="specific_numbers">Specific Phone Numbers</option>
                        </select>
                        @error('recipients_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Specific Numbers (shown when selected) -->
                    <div class="mb-3" id="specificNumbersField" style="display: none;">
                        <label class="form-label">Phone Numbers <span class="text-danger">*</span></label>
                        <textarea name="specific_numbers" id="specificNumbers" class="form-control" rows="3" placeholder="Enter phone numbers separated by commas&#10;Example: 01712345678, 01987654321, 01812345678"></textarea>
                        <div class="form-text">Enter multiple phone numbers separated by commas</div>
                        @error('specific_numbers')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Recipient Count Display -->
                    <div class="mb-3" id="recipientCountDisplay">
                        <div class="alert alert-info mb-0 py-2">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="recipientCountText">Select recipients to see count</span>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" id="message" class="form-control" rows="5" placeholder="Enter your message..." maxlength="1600" oninput="updateCharCount()"></textarea>
                        <div class="d-flex justify-content-between">
                            <div class="form-text">SMS will be sent as plain text</div>
                            <div class="form-text"><span id="charCount">0</span>/1600 characters</div>
                        </div>
                        @error('message')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Quick Messages -->
                    <div class="mb-3">
                        <label class="form-label">Quick Messages</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertQuickMessage('Hello! Thank you for shopping with us.')">
                                Welcome
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertQuickMessage('Your order has been shipped! Track it here:')">
                                Order Shipped
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertQuickMessage('Flash Sale! Get 50% off on all items today only. Shop now!')">
                                Flash Sale
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertQuickMessage('Your OTP code is: ')">
                                OTP Template
                            </button>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="mb-3">
                        <label class="form-label">Message Preview</label>
                        <div class="bg-light p-3 rounded" id="messagePreview" style="min-height: 60px; font-family: monospace;">
                            <span class="text-muted">Your message preview will appear here...</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <i class="bi bi-x-lg me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary" id="sendBtn" disabled>
                            <i class="bi bi-send me-1"></i> Send SMS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Tips -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Keep messages under 160 characters for single SMS</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Longer messages will be split into multiple SMS</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use +88 before phone numbers for international format</li>
                    <li class="mb-0"><i class="bi bi-check-circle text-success me-2"></i>Remove any spaces from phone numbers</li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-gear me-2"></i>SMS Gateway</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">Configure your SMS gateway in settings to enable actual SMS delivery.</p>
                <a href="#" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-sliders me-1"></i> SMS Settings
                </a>
            </div>
        </div>
    </div>
</div>

<!-- SMS History -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>SMS History</h6>
        
        <!-- Filter -->
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            @if(request('status'))
                <a href="{{ route('admin.marketing.bulk-sms.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Message</th>
                        <th>Recipients</th>
                        <th>Status</th>
                        <th>Sent Date</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($smsHistory as $sms)
                        <tr>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;" title="{{ $sms['message'] }}">
                                    {{ Str::limit($sms['message'], 50) }}
                                </div>
                            </td>
                            <td>
                                <div>{{ $sms['success_count'] }}/{{ $sms['total_recipients'] }}</div>
                                <small class="text-muted">
                                    @switch($sms['recipients_type'])
                                        @case('all_customers')
                                            All Customers
                                            @break
                                        @case('subscribers')
                                            Subscribers
                                            @break
                                        @case('registered_users')
                                            Registered Users
                                            @break
                                        @case('specific_numbers')
                                            Specific Numbers
                                            @break
                                        @default
                                            {{ $sms['recipients_type'] }}
                                    @endswitch
                                </small>
                            </td>
                            <td>
                                @switch($sms['status'])
                                    @case('sent')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sent</span>
                                        @break
                                    @case('partial')
                                        <span class="badge bg-warning"><i class="bi bi-exclamation-circle me-1"></i>Partial</span>
                                        @break
                                    @case('failed')
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Failed</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $sms['status'] }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($sms['created_at'])->format('M d, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($sms['created_at'])->format('h:i A') }}</small>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="viewSms('{{ $sms['id'] }}', {{ json_encode($sms['message']) }}, {{ $sms['total_recipients'] }}, {{ $sms['success_count'] }}, {{ $sms['failed_count'] }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSms('{{ $sms['id'] }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-phone text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-2 mt-2">No SMS campaigns found</p>
                                <p class="text-muted small">Send your first SMS using the form above</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($smsHistory->hasPages())
            <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Showing {{ $smsHistory->firstItem() }} - {{ $smsHistory->lastItem() }} of {{ $smsHistory->total() }} campaigns
                </div>
                <div>
                    {{ $smsHistory->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- SMS Detail Modal -->
<div class="modal fade" id="smsDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SMS Campaign Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <div class="bg-light p-3 rounded" id="detailMessage"></div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Total Recipients</label>
                        <div class="h5 mb-0" id="detailTotal"></div>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Status</label>
                        <div id="detailStatus"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="form-label">Successful</label>
                        <div class="h5 mb-0 text-success" id="detailSuccess"></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Failed</label>
                        <div class="h5 mb-0 text-danger" id="detailFailed"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Toggle specific numbers field
    document.getElementById('recipientsType').addEventListener('change', function() {
        const specificNumbersField = document.getElementById('specificNumbersField');
        const recipientCountDisplay = document.getElementById('recipientCountDisplay');
        
        if (this.value === 'specific_numbers') {
            specificNumbersField.style.display = 'block';
            recipientCountDisplay.style.display = 'none';
        } else {
            specificNumbersField.style.display = 'none';
            recipientCountDisplay.style.display = 'block';
        }
        
        updateSendButton();
    });

    // Update character count
    function updateCharCount() {
        const message = document.getElementById('message').value;
        document.getElementById('charCount').textContent = message.length;
        
        const preview = document.getElementById('messagePreview');
        if (message.trim()) {
            preview.innerHTML = message.replace(/\n/g, '<br>');
        } else {
            preview.innerHTML = '<span class="text-muted">Your message preview will appear here...</span>';
        }
        
        updateSendButton();
    }

    // Update recipient count
    function updateRecipientCount() {
        const type = document.getElementById('recipientsType').value;
        
        if (!type) {
            document.getElementById('recipientCountText').textContent = 'Select recipients to see count';
            document.getElementById('sendBtn').disabled = true;
            return;
        }
        
        if (type === 'specific_numbers') {
            const numbers = document.getElementById('specificNumbers').value;
            const count = numbers ? numbers.split(',').filter(n => n.trim()).length : 0;
            document.getElementById('recipientCountText').textContent = count + ' recipient(s)';
            document.getElementById('sendBtn').disabled = count === 0;
            return;
        }
        
        // Fetch count from server
        fetch('{{ route("admin.marketing.bulk-sms.recipient-count") }}?type=' + type)
            .then(response => response.json())
            .then(data => {
                document.getElementById('recipientCountText').textContent = data.count + ' recipient(s)';
                document.getElementById('sendBtn').disabled = data.count === 0;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('recipientCountText').textContent = 'Error getting count';
            });
        
        updateSendButton();
    }

    // Update send button state
    function updateSendButton() {
        const type = document.getElementById('recipientsType').value;
        const message = document.getElementById('message').value.trim();
        
        let hasRecipients = false;
        
        if (type === 'specific_numbers') {
            const numbers = document.getElementById('specificNumbers').value;
            hasRecipients = numbers && numbers.split(',').filter(n => n.trim()).length > 0;
        } else if (type) {
            const countText = document.getElementById('recipientCountText').textContent;
            const match = countText.match(/(\d+)/);
            hasRecipients = match && parseInt(match[1]) > 0;
        }
        
        document.getElementById('sendBtn').disabled = !type || !message || !hasRecipients;
    }

    // Insert quick message
    function insertQuickMessage(text) {
        document.getElementById('message').value = text;
        updateCharCount();
    }

    // Reset form
    function resetForm() {
        document.getElementById('smsForm').reset();
        document.getElementById('specificNumbersField').style.display = 'none';
        document.getElementById('recipientCountText').textContent = 'Select recipients to see count';
        document.getElementById('charCount').textContent = '0';
        document.getElementById('messagePreview').innerHTML = '<span class="text-muted">Your message preview will appear here...</span>';
        document.getElementById('sendBtn').disabled = true;
    }

    // View SMS details
    function viewSms(id, message, total, success, failed) {
        document.getElementById('detailMessage').textContent = message;
        document.getElementById('detailTotal').textContent = total;
        
        let statusHtml = '';
        if (failed === 0) {
            statusHtml = '<span class="badge bg-success">Sent</span>';
        } else if (success > 0) {
            statusHtml = '<span class="badge bg-warning">Partial</span>';
        } else {
            statusHtml = '<span class="badge bg-danger">Failed</span>';
        }
        document.getElementById('detailStatus').innerHTML = statusHtml;
        
        document.getElementById('detailSuccess').textContent = success;
        document.getElementById('detailFailed').textContent = failed;
        
        new bootstrap.Modal(document.getElementById('smsDetailModal')).show();
    }

    // Delete SMS campaign
    function deleteSms(id) {
        if (!confirm('Are you sure you want to delete this SMS campaign?')) {
            return;
        }
        
        const url = '{{ url("admin/marketing/bulk-sms") }}/' + id;
        
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting SMS campaign');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting SMS campaign');
        });
    }

    // Update count when specific numbers change
    document.getElementById('specificNumbers').addEventListener('input', updateRecipientCount);
</script>
@endpush
@endsection
