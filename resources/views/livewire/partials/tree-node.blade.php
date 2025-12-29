@props(['node', 'isRoot' => false])

<div class="flex flex-col items-center">
    
    {{-- 1. KARTU DATA (NODE) --}}
    <div class="relative z-10 flex flex-col items-center">
        {{-- Memanggil komponen isi kartu (Input/Dropdown ada di sini) --}}
        @include('livewire.partials.manual-card', ['node' => $node])
    </div>

    {{-- 2. LOGIC ANAK (BRANCHES) --}}
    @if($node->children->count() > 0)
        
        {{-- Garis Vertikal Turun dari Parent --}}
        <div class="h-12 w-px bg-gray-400"></div>

        {{-- Container Anak (Horizontal Flex) --}}
        <div class="flex justify-center relative">
            
            {{-- Garis Horizontal Penghubung (Crossbar) --}}
            {{-- Logic: Garis ini membentang dari anak pertama sampai anak terakhir --}}
            @if($node->children->count() > 1)
                <div class="absolute top-0 h-px bg-gray-400" 
                     style="left: calc(50% / {{ $node->children->count() }} + 1px); right: calc(50% / {{ $node->children->count() }} + 1px);">
                </div>
            @endif

            {{-- Looping Anak --}}
            @foreach($node->children as $child)
                <div class="flex flex-col items-center relative px-6">
                    
                    {{-- Garis Vertikal Naik ke Crossbar --}}
                    {{-- (Hanya muncul jika bukan Root) --}}
                    <div class="h-12 w-px bg-gray-400"></div>

                    {{-- Rekursif: Panggil tree-node ini lagi untuk anak tersebut --}}
                    @include('livewire.partials.tree-node', ['node' => $child, 'isRoot' => false])
                </div>
            @endforeach
        </div>
    @endif
</div>