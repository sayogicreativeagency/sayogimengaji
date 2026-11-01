@props(['q' => '', 'derajat' => null, 'kitab' => null, 'filter' => false])
<form action="/cari" method="get" class="space-y-3">
    <div class="flex gap-2">
        <label for="q" class="sr-only">Cari hadist</label>
        <input id="q" name="q" type="search" value="{{ $q }}" required maxlength="200"
               placeholder="mis. hadist tentang pernikahan"
               class="w-full rounded-lg border border-stone-300 bg-white px-4 py-3 text-base shadow-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 focus:outline-none dark:border-stone-700 dark:bg-stone-900">
        <button class="rounded-lg bg-emerald-700 px-5 py-3 font-medium text-white hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 focus-visible:outline-none">Cari</button>
    </div>

    @if ($filter)
        <div class="flex flex-wrap gap-2 text-sm">
            <label class="sr-only" for="derajat">Derajat</label>
            <select id="derajat" name="derajat" onchange="this.form.submit()" class="rounded-md border border-stone-300 bg-white px-3 py-1.5 dark:border-stone-700 dark:bg-stone-900">
                <option value="">Semua derajat</option>
                @foreach (App\Enums\Derajat::cases() as $d)
                    <option value="{{ $d->value }}" @selected($derajat === $d)>{{ $d->label() }}</option>
                @endforeach
            </select>
            <label class="sr-only" for="kitab">Kitab</label>
            <select id="kitab" name="kitab" onchange="this.form.submit()" class="rounded-md border border-stone-300 bg-white px-3 py-1.5 dark:border-stone-700 dark:bg-stone-900">
                <option value="">Semua kitab</option>
                @foreach (App\Enums\Kitab::cases() as $k)
                    <option value="{{ $k->value }}" @selected($kitab === $k)>{{ $k->label() }}</option>
                @endforeach
            </select>
        </div>
    @endif
</form>
