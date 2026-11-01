@props(['nama', 'label', 'type' => 'text'])
<div>
    <label for="{{ $nama }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
    <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $type }}" @unless (str_starts_with($type, 'password')) value="{{ old($nama) }}" @endunless
           {{ $attributes->class('w-full rounded-lg border border-stone-300 bg-white px-3 py-2.5 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 focus:outline-none dark:border-stone-700 dark:bg-stone-900') }}>
    @error($nama) <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
</div>
