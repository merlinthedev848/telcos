@csrf
<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Company Name (Optional)</label>
        <input
            type="text"
            name="company_name"
            value="{{ old('company_name', $customer->company_name ?? '') }}"
            placeholder="e.g. Acme Telecom Ltd"
            class="mt-1.5 w-full rounded-lg border border-line px-3.5 py-2 text-sm text-ink shadow-sm focus:border-action focus:outline-none focus:ring-1 focus:ring-action"
        >
    </div>

    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Primary Contact Name <span class="text-rose-500">*</span></label>
        <input
            type="text"
            name="contact_name"
            value="{{ old('contact_name', $customer->contact_name ?? '') }}"
            required
            placeholder="e.g. Jane Doe"
            class="mt-1.5 w-full rounded-lg border border-line px-3.5 py-2 text-sm text-ink shadow-sm focus:border-action focus:outline-none focus:ring-1 focus:ring-action"
        >
    </div>

    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Email Address <span class="text-rose-500">*</span></label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $customer->email ?? '') }}"
            required
            placeholder="e.g. contact@example.com"
            class="mt-1.5 w-full rounded-lg border border-line px-3.5 py-2 text-sm text-ink shadow-sm focus:border-action focus:outline-none focus:ring-1 focus:ring-action"
        >
    </div>

    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Phone Number</label>
        <input
            type="tel"
            name="phone"
            value="{{ old('phone', $customer->phone ?? '') }}"
            placeholder="e.g. +44 20 7946 0100"
            class="mt-1.5 w-full rounded-lg border border-line px-3.5 py-2 text-sm text-ink shadow-sm focus:border-action focus:outline-none focus:ring-1 focus:ring-action"
        >
    </div>

    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Account Status</label>
        <select
            name="status"
            class="mt-1.5 w-full rounded-lg border border-line px-3.5 py-2 text-sm text-ink shadow-sm focus:border-action focus:outline-none focus:ring-1 focus:ring-action bg-white"
        >
            @foreach(\App\Enums\CustomerStatus::cases() as $statusCase)
                <option value="{{ $statusCase->value }}" {{ old('status', $customer->status ?? 'lead') === $statusCase->value ? 'selected' : '' }}>
                    {{ ucfirst($statusCase->value) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Service Location Address</label>
        <textarea
            name="service_address"
            rows="3"
            placeholder="Installation premises address..."
            class="mt-1.5 w-full rounded-lg border border-line px-3.5 py-2 text-sm text-ink shadow-sm focus:border-action focus:outline-none focus:ring-1 focus:ring-action"
        >{{ old('service_address', $customer->service_address ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Billing Address</label>
        <textarea
            name="billing_address"
            rows="3"
            placeholder="Billing invoice address (leave blank if identical to service address)..."
            class="mt-1.5 w-full rounded-lg border border-line px-3.5 py-2 text-sm text-ink shadow-sm focus:border-action focus:outline-none focus:ring-1 focus:ring-action"
        >{{ old('billing_address', $customer->billing_address ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3 border-t border-line pt-4">
    <a href="{{ route('customers.index') }}" class="rounded-lg border border-line px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
        Cancel
    </a>
    <button type="submit" class="rounded-lg bg-action px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
        Save Customer
    </button>
</div>
