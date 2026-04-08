<div class="bg-white shadow-xl shadow-slate-200/50 border border-slate-100/50 rounded-2xl overflow-hidden transition-all">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/80 backdrop-blur-sm">
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
        <div class="px-6 py-5 bg-slate-50/50 border-t border-slate-100/80">
            {{ $footer }}
        </div>
    @endif
</div>
