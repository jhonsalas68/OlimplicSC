<div class="bg-white shadow-sm border border-slate-100 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/50">
                <tr>
                    {{ $header }}
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-50">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    @if(isset($footer))
        <div class="px-6 py-4 bg-slate-50/30 border-t border-slate-100 italic text-xs text-slate-400">
            {{ $footer }}
        </div>
    @endif
</div>
