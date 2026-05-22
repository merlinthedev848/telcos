@csrf
<div class="grid gap-4 md:grid-cols-2">
    <label class="block text-sm font-medium">Company
        <input name="company_name" value="{{ old('company_name', $customer->company_name ?? '') }}" class="mt-1 w-full border-line">
    </label>
    <label class="block text-sm font-medium">Contact
        <input name="contact_name" value="{{ old('contact_name', $customer->contact_name ?? '') }}" required class="mt-1 w-full border-line">
    </label>
    <label class="block text-sm font-medium">Email
        <input name="email" type="email" value="{{ old('email', $customer->email ?? '') }}" required class="mt-1 w-full border-line">
    </label>
    <label class="block text-sm font-medium">Phone
        <input name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="mt-1 w-full border-line">
    </label>
    <label class="block text-sm font-medium md:col-span-2">Service Address
        <textarea name="service_address" rows="3" class="mt-1 w-full border-line">{{ old('service_address', $customer->service_address ?? '') }}</textarea>
    </label>
    <label class="block text-sm font-medium md:col-span-2">Billing Address
        <textarea name="billing_address" rows="3" class="mt-1 w-full border-line">{{ old('billing_address', $customer->billing_address ?? '') }}</textarea>
    </label>
</div>
<button class="mt-5 bg-action px-4 py-2 text-sm font-semibold text-white">Save Customer</button>
