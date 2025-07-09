{{-- resources/views/admin/customers/_form.blade.php --}}
@csrf

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">معلومات العميل الأساسية</div>
            <div class="card-body">
                {{-- Name --}}
                <div class="form-group mb-3">
                    <label for="name">الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $customer->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 {{-- Email --}}
                <div class="form-group mb-3">
                    <label for="email">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $customer->email ?? '') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 {{-- Phone --}}
                <div class="form-group mb-3">
                    <label for="phone">رقم الهاتف <span class="text-danger">*</span></label>
                    <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $customer->phone ?? '') }}" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Password Fields --}}
                <hr>
                <h6 class="mb-3">{{ isset($customer) ? 'تغيير كلمة المرور (اختياري)' : 'كلمة المرور' }}</h6>
                <div class="row">
                     <div class="col-md-6 form-group mb-3">
                         <label for="password">كلمة المرور <span class="text-danger">{{ isset($customer) ? '' : '*' }}</span></label>
                         <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ isset($customer) ? '' : 'required' }} autocomplete="new-password">
                         @if(isset($customer)) <small class="form-text text-muted">اتركه فارغًا لعدم التغيير.</small> @endif
                         @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                     </div>
                      <div class="col-md-6 form-group mb-3">
                         <label for="password_confirmation">تأكيد كلمة المرور <span class="text-danger">{{ isset($customer) ? '' : '*' }}</span></label>
                         <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" {{ isset($customer) ? '' : 'required' }} autocomplete="new-password">
                     </div>
                 </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">الحالة والرصيد</div>
            <div class="card-body">
                {{-- Status --}}
                <div class="form-group mb-3">
                    <label for="status">حالة الحساب <span class="text-danger">*</span></label>
                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $customer->status ?? App\Models\Customer::STATUS_ACTIVE) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Balance --}}
                <div class="form-group mb-3">
                    <label for="balance">الرصيد (AED)</label>
                    <input type="number" id="balance" name="balance" class="form-control @error('balance') is-invalid @enderror"
                           value="{{ old('balance', $customer->balance ?? '0.00') }}" step="0.01" min="0">
                    @error('balance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Banned Status (Typically managed via Ban/Unban actions, but can be included for direct creation) --}}
                @if(!isset($customer) || ($customer && $customer->status !== \App\Models\Customer::STATUS_BANNED && !$customer->is_banned)) {{-- Show only on create or if not currently banned through status --}}
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_banned" name="is_banned" value="1"
                               {{ old('is_banned', $customer->is_banned ?? false) ? 'checked' : '' }} onchange="toggleBanReason()">
                        <label class="form-check-label" for="is_banned">حظر هذا العميل</label>
                    </div>
                </div>
                <div class="form-group mb-3" id="ban_reason_group" style="{{ old('is_banned', $customer->is_banned ?? false) ? '' : 'display:none;' }}">
                    <label for="ban_reason">سبب الحظر <span class="text-danger" id="ban_reason_required_star" style="display:none;">*</span></label>
                    <textarea id="ban_reason" name="ban_reason" class="form-control @error('ban_reason') is-invalid @enderror"
                              rows="3">{{ old('ban_reason', $customer->ban_reason ?? '') }}</textarea>
                    @error('ban_reason') <div class="invalid-feedback">{{ $message ?? '' }}</div> @enderror
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm ms-1"/> {{ isset($customer) ? 'تحديث العميل' : 'إنشاء عميل' }}
    </button>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">إلغاء</a>
</div>

@push('scripts')
<script>
    function toggleBanReason() {
        const isBannedCheckbox = document.getElementById('is_banned');
        const banReasonGroup = document.getElementById('ban_reason_group');
        const banReasonRequiredStar = document.getElementById('ban_reason_required_star');
        const banReasonTextarea = document.getElementById('ban_reason');

        if (isBannedCheckbox && banReasonGroup && banReasonRequiredStar) {
            if (isBannedCheckbox.checked) {
                banReasonGroup.style.display = 'block';
                banReasonRequiredStar.style.display = 'inline';
                banReasonTextarea.required = true;
            } else {
                banReasonGroup.style.display = 'none';
                banReasonRequiredStar.style.display = 'none';
                banReasonTextarea.required = false;
            }
        }
    }
    // Run on page load to set initial state
    document.addEventListener('DOMContentLoaded', toggleBanReason);
</script>
@endpush

{{-- RTL adjustments if needed --}}
@push('styles')
<style>
    html[dir="rtl"] .form-actions .btn:first-child { margin-left: 0.5rem; margin-right: 0; }
    html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
</style>
@endpush