@props(['title' => ''])

<div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700/50 shadow-xl overflow-hidden">
    @if($title)
        <div class="p-4 border-b border-gray-700/50">
            <h3 class="text-lg font-semibold text-white">{{ $title }}</h3>
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>