@props(['q' => ''])
<form action="/cari" method="get">
    <div class="flex gap-2">
        <label for="q" class="sr-only">Cari hadist</label>
        <input id="q" name="q" type="search" value="{{ $q }}" required maxlength="200"
               placeholder="mis. hadist tentang pernikahan"
               class="w-full rounded-lg border border-stone-300 bg-white px-4 py-3 text-base shadow-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 focus:outline-none dark:border-stone-700 dark:bg-stone-900">
        <button class="rounded-lg bg-emerald-700 px-5 py-3 font-medium text-white hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 focus-visible:outline-none">Cari</button>
    </div>

</form>
