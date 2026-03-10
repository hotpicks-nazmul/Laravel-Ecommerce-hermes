@extends('admin.layouts.app')

@section('title', 'Chat Widget Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Chat Widget Settings</h4>
    <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Live Chat
    </a>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <!-- Settings Form -->
    <div class="col-lg-8">
        <form action="{{ route('admin.chat.widget-settings') }}" method="POST" id="widget-settings-form">
            @csrf
            
            <!-- Widget Appearance -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-palette me-2"></i>Widget Appearance</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Widget Position</label>
                            <select class="form-select" name="widget_position">
                                <option value="bottom-right" selected>Bottom Right</option>
                                <option value="bottom-left">Bottom Left</option>
                                <option value="top-right">Top Right</option>
                                <option value="top-left">Top Left</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Widget Theme Color</label>
                            <div class="d-flex align-items-center">
                                <input type="color" class="form-control form-control-color me-2" name="widget_color" value="#0d6efd">
                                <span class="text-muted small">Primary color for the widget</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Floating Button Text</label>
                        <input type="text" class="form-control" name="widget_button_text" value="Chat" placeholder="Chat">
                        <div class="form-text">Text label on the floating chat button</div>
                    </div>
                </div>
            </div>

            <!-- Welcome Messages -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-chat-square-text me-2"></i>Welcome Messages</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Welcome Message (Customer Chat)</label>
                        <input type="text" name="chat_welcome_message" class="form-control" 
                            value="{{ \App\Models\Setting::get('chat_welcome_message', 'Hello! How can I help you today?') }}">
                        <div class="form-text">Message shown when customer opens chat widget</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Welcome Subtitle</label>
                        <input type="text" name="chat_welcome_subtitle" class="form-control" 
                            value="{{ \App\Models\Setting::get('chat_welcome_subtitle', 'Our team typically replies within minutes') }}">
                        <div class="form-text">Subtitle shown below the welcome message</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Offline Message</label>
                        <input type="text" name="chat_offline_message" class="form-control" 
                            value="{{ \App\Models\Setting::get('chat_offline_message', 'We are currently offline. Leave us a message!') }}">
                        <div class="form-text">Message shown when no agents are online</div>
                    </div>
                </div>
            </div>

            <!-- Auto-Reply Messages -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-chat-heart me-2"></i>Auto-Reply Messages (Chatbot)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Configure automatic replies for common customer queries. These replies are triggered by specific keywords.
                    </p>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <span class="badge bg-info me-1">Greeting</span> 
                            Trigger: hello, hi, salam, hey
                        </label>
                        <textarea class="form-control" name="chat_reply_greeting" rows="2">{{ \App\Models\Setting::get('chat_reply_greeting', 'Wa Alaikum Assalam! Welcome to Halal Food Store. How can I assist you today?') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <span class="badge bg-primary me-1">Delivery</span> 
                            Trigger: delivery, area, shipping, send, poriman
                        </label>
                        <textarea class="form-control" name="chat_reply_delivery" rows="2">{{ \App\Models\Setting::get('chat_reply_delivery', 'We deliver across Bangladesh! Dhaka: Same day delivery. Other areas: 1-3 business days. Free delivery on orders over ৳500!') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <span class="badge bg-success me-1">Payment</span> 
                            Trigger: payment, bkash, nagad, cash, method
                        </label>
                        <textarea class="form-control" name="chat_reply_payment" rows="2">{{ \App\Models\Setting::get('chat_reply_payment', 'We accept multiple payment methods: bKash, Nagad, Rocket, Credit/Debit Cards, and Cash on Delivery.') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <span class="badge bg-warning me-1">Order</span> 
                            Trigger: order, track, delivery status, kon din asbe
                        </label>
                        <textarea class="form-control" name="chat_reply_track_order" rows="2">{{ \App\Models\Setting::get('chat_reply_track_order', 'To track your order, please. You can also provide your order number check your order status in My Orders section.') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <span class="badge bg-danger me-1">Return</span> 
                            Trigger: return, refund, change, bad, problem
                        </label>
                        <textarea class="form-control" name="chat_reply_return" rows="2">{{ \App\Models\Setting::get('chat_reply_return', 'We have a hassle-free return policy! If not satisfied, contact us within 24 hours for refund or replacement.') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <span class="badge bg-secondary me-1">Quality</span> 
                            Trigger: halal, quality, masala, pure, taqwa
                        </label>
                        <textarea class="form-control" name="chat_reply_halal" rows="2">{{ \App\Models\Setting::get('chat_reply_halal', 'All our products are 100% Halal certified! We source from trusted suppliers and maintain strict quality standards.') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <span class="badge bg-dark me-1">Price</span> 
                            Trigger: price, cost, offer, discount, deal
                        </label>
                        <textarea class="form-control" name="chat_reply_price" rows="2">{{ \App\Models\Setting::get('chat_reply_price', 'Our prices are competitive and transparent. Check our Deals section for special discounts!') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <span class="badge bg-light text-dark me-1">Contact</span> 
                            Trigger: contact, phone, number, call, email
                        </label>
                        <textarea class="form-control" name="chat_reply_contact" rows="2">{{ \App\Models\Setting::get('chat_reply_contact', 'You can reach us at: Phone: 019XX-XXXXXX, Email: support@halalfoodstore.com') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Advanced Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Advanced Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="enableSound" name="enable_sound" checked>
                                <label class="form-check-label" for="enableSound">Enable Sound Notifications</label>
                            </div>
                            <div class="form-text">Play sound when new message arrives</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="enableDesktopNotify" name="enable_desktop_notify" checked>
                                <label class="form-check-label" for="enableDesktopNotify">Desktop Notifications</label>
                            </div>
                            <div class="form-text">Show browser notifications for new messages</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="showOnlineStatus" name="show_online_status" checked>
                                <label class="form-check-label" for="showOnlineStatus">Show Online Status</label>
                            </div>
                            <div class="form-text">Display online/offline status to customers</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="enableCannedResponses" name="enable_canned_responses" checked>
                                <label class="form-check-label" for="enableCannedResponses">Enable Quick Replies</label>
                            </div>
                            <div class="form-text">Show quick reply buttons to customers</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Panel -->
    <div class="col-lg-4">
        <!-- Widget Preview -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-eye me-2"></i>Widget Preview</h6>
            </div>
            <div class="card-body p-0">
                <div class="bg-light p-3" style="min-height: 350px; position: relative;">
                    <!-- Floating Button -->
                    <div style="position: absolute; bottom: 10px; right: 10px;">
                        <button class="btn btn-primary rounded-circle p-3 shadow">
                            <i class="bi bi-chat-dots fs-5"></i>
                        </button>
                    </div>
                    
                    <!-- Chat Window Preview -->
                    <div class="bg-white rounded shadow-sm" style="position: absolute; bottom: 70px; right: 10px; width: 280px;">
                        <!-- Header -->
                        <div class="bg-primary text-white p-3 rounded-top" style="position: relative;">
                            <div class="d-flex align-items-center">
                                <div class="bg-white rounded-circle p-2 me-2">
                                    <i class="bi bi-headset text-primary"></i>
                                </div>
                                <div>
                                    <strong>Customer Support</strong>
                                    <div class="small opacity-75">Online</div>
                                </div>
                            </div>
                        </div>
                        <!-- Messages -->
                        <div class="p-3" style="max-height: 200px; overflow-y: auto;">
                            <div class="mb-2">
                                <div class="bg-light rounded p-2">
                                    <small><strong>Welcome!</strong></small><br>
                                    <small>{{ \App\Models\Setting::get('chat_welcome_message', 'Hello! How can I help you today?') }}</small>
                                </div>
                                <small class="text-muted">Just now</small>
                            </div>
                            <div class="mb-2 text-end">
                                <div class="bg-primary text-white rounded p-2">
                                    <small>I want to know about delivery</small>
                                </div>
                                <small class="text-muted">Just now</small>
                            </div>
                            <div class="mb-2">
                                <div class="bg-light rounded p-2">
                                    <small>{{ \App\Models\Setting::get('chat_reply_delivery', 'We deliver across Bangladesh!') }}</small>
                                </div>
                                <small class="text-muted">Just now</small>
                            </div>
                        </div>
                        <!-- Input -->
                        <div class="p-2 border-top">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" placeholder="Type a message...">
                                <button class="btn btn-primary"><i class="bi bi-send"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Replies Preview -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Reply Triggers</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-1 mb-2">
                    <span class="badge bg-secondary">hello</span>
                    <span class="badge bg-secondary">delivery</span>
                    <span class="badge bg-secondary">payment</span>
                    <span class="badge bg-secondary">order</span>
                </div>
                <div class="d-flex flex-wrap gap-1">
                    <span class="badge bg-secondary">return</span>
                    <span class="badge bg-secondary">halal</span>
                    <span class="badge bg-secondary">price</span>
                    <span class="badge bg-secondary">contact</span>
                </div>
                <hr>
                <small class="text-muted">Customers can type these keywords to get instant auto-replies</small>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-question-circle me-2"></i>Help & Tips</h6>
            </div>
            <div class="card-body">
                <ul class="small text-muted mb-0 ps-3">
                    <li class="mb-2">Use keywords that customers commonly use</li>
                    <li class="mb-2">Keep auto-replies short and clear</li>
                    <li class="mb-2">Include call-to-action in replies</li>
                    <li class="mb-2">Test the widget after making changes</li>
                    <li>Monitor chat analytics to improve responses</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
