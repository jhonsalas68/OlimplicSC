@props(['href', 'icon', 'label', 'active' => false])

<a href="{{ $href }}" data-turbo-prefetch="true"
   @click="if (window.innerWidth < 768) sidebarOpen = false"
   class="flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 group/link
          {{ $active 
            ? 'bg-white/10 text-white shadow-lg shadow-black/5 border-l-4 border-red-500' 
            : 'text-blue-100 hover:bg-white/5 hover:text-white border-l-4 border-transparent' }}">
    <svg class="flex-shrink-0 h-5 w-5 transition-transform duration-300 group-hover/link:scale-110 
                {{ $active ? 'text-white' : 'text-blue-200 group-hover/link:text-blue-100' }}" 
         fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
    </svg>
    <span class="ml-4 whitespace-nowrap transition-opacity duration-300"
          :class="sidebarOpen ? 'opacity-100' : 'opacity-0 group-hover/sidebar:opacity-100'">
        {{ $label }}
    </span>
</a>
