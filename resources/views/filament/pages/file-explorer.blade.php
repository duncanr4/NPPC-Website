<x-filament-panels::page>
    <style>
        .fe-table { width: 100%; border-collapse: collapse; }
        .fe-table th { text-align: left; padding: 8px 12px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255,255,255,0.5); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .fe-table td { padding: 6px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }
        .fe-row { cursor: pointer; transition: background 0.1s; }
        .fe-row:hover { background: rgba(255,255,255,0.05); }
        .fe-icon { width: 20px; height: 20px; display: inline-block; vertical-align: middle; margin-right: 8px; }
        .fe-folder { color: #fbbf24; }
        .fe-file { color: #94a3b8; }
        .fe-php { color: #7c6ccf; }
        .fe-js { color: #f7df1e; }
        .fe-vue { color: #42b883; }
        .fe-css { color: #38bdf8; }
        .fe-json { color: #f97316; }
        .fe-img { color: #ec4899; }
        .fe-db { color: #22c55e; }
        .fe-md { color: #60a5fa; }
        .fe-video { color: #a855f7; }
        .fe-pdf { color: #ef4444; }
        .fe-breadcrumb { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
        .fe-breadcrumb-item { font-size: 14px; cursor: pointer; color: rgba(255,255,255,0.6); }
        .fe-breadcrumb-item:hover { color: #fff; }
        .fe-breadcrumb-sep { color: rgba(255,255,255,0.3); font-size: 12px; }
        .fe-breadcrumb-active { color: #fff; font-weight: 600; }
        .fe-toolbar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .fe-search-input { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 14px; width: 280px; outline: none; }
        .fe-search-input:focus { border-color: #6366f1; }
        .fe-search-input::placeholder { color: rgba(255,255,255,0.3); }
        .fe-small-input { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 6px 10px; color: #fff; font-size: 13px; width: 180px; outline: none; }
        .fe-small-input:focus { border-color: #6366f1; }
        .fe-small-input::placeholder { color: rgba(255,255,255,0.3); }
        .fe-btn { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 6px 14px; color: #fff; font-size: 13px; cursor: pointer; transition: background 0.15s; }
        .fe-btn:hover { background: rgba(255,255,255,0.15); }
        .fe-btn-primary { background: #6366f1; border-color: #6366f1; }
        .fe-btn-primary:hover { background: #5558e6; }
        .fe-search-result-dir { font-size: 11px; color: rgba(255,255,255,0.35); margin-left: 28px; }
    </style>

    {{-- Top Bar: Breadcrumbs + Search + Actions --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 16px; flex-wrap: wrap;">
        {{-- Breadcrumbs --}}
        <div class="fe-breadcrumb">
            @foreach($breadcrumbs as $crumb)
                @if(!$loop->last)
                    <span class="fe-breadcrumb-item" wire:click="navigateTo('{{ $crumb['path'] }}')">
                        @if($loop->first)
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:2px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        @endif
                        {{ $crumb['name'] }}
                    </span>
                    <span class="fe-breadcrumb-sep">/</span>
                @else
                    <span class="fe-breadcrumb-active">{{ $crumb['name'] }}</span>
                @endif
            @endforeach
        </div>

        {{-- Search --}}
        <div class="fe-toolbar">
            <form wire:submit="search" style="display: flex; gap: 6px;">
                <input type="text" wire:model="searchQuery" class="fe-search-input" placeholder="Search files... (e.g. .png or filename)">
                <button type="submit" class="fe-btn">Search</button>
                @if($isSearching)
                    <button type="button" wire:click="clearSearch" class="fe-btn">&times; Clear</button>
                @endif
            </form>
        </div>
    </div>

    {{-- Actions Bar: New Folder + Upload --}}
    <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 16px; flex-wrap: wrap;">
        {{-- New Folder --}}
        <form wire:submit="createFolder" style="display: flex; gap: 6px; align-items: center;">
            <input type="text" wire:model="newFolderName" class="fe-small-input" placeholder="New folder name">
            <button type="submit" class="fe-btn">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:4px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
                Create Folder
            </button>
        </form>

        {{-- Upload --}}
        <form wire:submit="uploadFiles" style="display: flex; gap: 6px; align-items: center;">
            <input type="file" wire:model="uploadedFiles" multiple style="font-size: 13px; color: rgba(255,255,255,0.7);">
            @if($uploadedFiles)
                <button type="submit" class="fe-btn fe-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:4px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    Upload ({{ count($uploadedFiles) }} file{{ count($uploadedFiles) > 1 ? 's' : '' }})
                </button>
            @endif
        </form>

        <span style="font-size: 12px; color: rgba(255,255,255,0.3);">Uploading to: /{{ $currentPath ?: 'project root' }}</span>
    </div>

    {{-- Search Results --}}
    @if($isSearching)
        <x-filament::section heading="Search Results ({{ count($searchResults) }}{{ count($searchResults) >= 100 ? '+' : '' }})">
            @if(empty($searchResults))
                <div style="text-align: center; color: rgba(255,255,255,0.4); padding: 24px;">No results found for "{{ $searchQuery }}"</div>
            @else
                <table class="fe-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th style="width: 200px;">Location</th>
                            <th style="width: 100px; text-align: right;">Size</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($searchResults as $result)
                            <tr class="fe-row"
                                @if($result['is_dir'])
                                    wire:click="navigateTo('{{ $result['path'] }}')"
                                @else
                                    wire:click="viewFile('{{ $result['path'] }}')"
                                @endif
                            >
                                <td>
                                    @if($result['is_dir'])
                                        <svg xmlns="http://www.w3.org/2000/svg" class="fe-icon fe-folder" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="fe-icon fe-{{ $this->getFileIcon($result['ext'] ?? '') }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                    @endif
                                    {{ $result['name'] }}
                                </td>
                                <td style="font-size: 12px; color: rgba(255,255,255,0.4); font-family: monospace;">{{ $result['dir'] }}</td>
                                <td style="text-align: right; color: rgba(255,255,255,0.4); font-size: 13px;">
                                    {{ $result['is_dir'] ? '-' : $this->formatSize($result['size']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-filament::section>
    @endif

    {{-- Main Content --}}
    @if(!$isSearching)
        <div style="display: flex; gap: 24px;">
            {{-- File List --}}
            <div style="flex: {{ $fileContent !== null ? '1' : '1' }}; min-width: 0;">
                <x-filament::section>
                    @if($currentPath)
                        <div class="fe-row" wire:click="goUp" style="padding: 8px 12px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;color:#fbbf24;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                            <span style="color: rgba(255,255,255,0.6); font-size: 14px;">..</span>
                        </div>
                    @endif

                    <table class="fe-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th style="width: 100px; text-align: right;">Size</th>
                                <th style="width: 160px; text-align: right;">Modified</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr class="fe-row"
                                    @if($item['is_dir'])
                                        wire:click="navigateTo('{{ $item['path'] }}')"
                                    @else
                                        wire:click="viewFile('{{ $item['path'] }}')"
                                    @endif
                                >
                                    <td>
                                        @if($item['is_dir'])
                                            <svg xmlns="http://www.w3.org/2000/svg" class="fe-icon fe-folder" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="fe-icon fe-{{ $this->getFileIcon($item['ext'] ?? '') }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                        @endif
                                        <span style="{{ $item['is_dir'] ? 'font-weight: 600;' : '' }}">{{ $item['name'] }}</span>
                                    </td>
                                    <td style="text-align: right; color: rgba(255,255,255,0.4); font-size: 13px;">
                                        {{ $item['is_dir'] ? '-' : $this->formatSize($item['size']) }}
                                    </td>
                                    <td style="text-align: right; color: rgba(255,255,255,0.4); font-size: 13px;">
                                        {{ date('M j, Y g:ia', $item['modified']) }}
                                    </td>
                                </tr>
                            @endforeach

                            @if(empty($items))
                                <tr>
                                    <td colspan="3" style="text-align: center; color: rgba(255,255,255,0.4); padding: 24px;">Empty directory</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </x-filament::section>
            </div>

            {{-- File Viewer Panel --}}
            @if($fileContent !== null)
                <div style="flex: 1; min-width: 0;">
                    <x-filament::section>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; margin-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <span class="font-mono" style="font-size: 13px; color: rgba(255,255,255,0.8);">{{ $viewingFile }}</span>
                            <button wire:click="closeFile" style="color: rgba(255,255,255,0.5); cursor: pointer; background: none; border: none; font-size: 20px; line-height: 1;">&times;</button>
                        </div>
                        @if($fileContent === '__IMAGE__' && $imageUrl)
                            <div style="text-align: center; padding: 16px;">
                                <img src="{{ $imageUrl }}" style="max-width: 100%; max-height: 500px; border-radius: 4px;">
                            </div>
                        @else
                            <div style="max-height: 600px; overflow: auto;">
                                <pre class="whitespace-pre-wrap text-xs font-mono bg-gray-900 text-gray-100 p-4" style="margin: 0; border-radius: 6px;">{{ $fileContent }}</pre>
                            </div>
                        @endif
                    </x-filament::section>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>
