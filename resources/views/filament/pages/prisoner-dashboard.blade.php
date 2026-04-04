<x-filament-panels::page>
    {{-- Status Overview Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600">{{ number_format($totalPrisoners) }}</div>
                <div class="text-sm text-gray-500 mt-1">Total Prisoners</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-danger-600">{{ number_format($inCustody) }}</div>
                <div class="text-sm text-gray-500 mt-1">In Custody</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-warning-600">{{ number_format($inExile) }}</div>
                <div class="text-sm text-gray-500 mt-1">In Exile</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-success-600">{{ number_format($released) }}</div>
                <div class="text-sm text-gray-500 mt-1">Released</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-700 dark:text-gray-300">{{ number_format($awaitingTrial) }}</div>
                <div class="text-sm text-gray-500 mt-1">Awaiting Trial</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-700 dark:text-gray-300">{{ number_format($imprisonedOrExiled) }}</div>
                <div class="text-sm text-gray-500 mt-1">Imprisoned or Exiled</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600">{{ number_format($accumulatedDaysImprisoned) }}</div>
                <div class="text-sm text-gray-500 mt-1">Collective Days Imprisoned</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-warning-600">{{ number_format($accumulatedDaysInExile) }}</div>
                <div class="text-sm text-gray-500 mt-1">Collective Days in Exile</div>
            </div>
        </x-filament::section>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Gender Chart --}}
        <x-filament::section heading="Gender Breakdown">
            <div class="space-y-2">
                @foreach($genderCounts as $label => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label ?: 'Unknown' }}</span>
                        <span class="text-sm font-bold">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="h-3 rounded-full" style="width: {{ $totalPrisoners > 0 ? round(($count / $totalPrisoners) * 100) : 0 }}%; background-color: {{ ['#825af9', '#35d9c3', '#4375ff', '#6d3dbf', '#2cb1a1'][($loop->index % 5)] }}"></div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Race Chart --}}
        <x-filament::section heading="Racial Composition">
            <div class="space-y-2">
                @foreach($raceCounts as $label => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label ?: 'Unknown' }}</span>
                        <span class="text-sm font-bold">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="h-3 rounded-full" style="width: {{ $totalPrisoners > 0 ? round(($count / $totalPrisoners) * 100) : 0 }}%; background-color: {{ ['#825af9', '#35d9c3', '#4375ff', '#6d3dbf', '#2cb1a1', '#3658d4'][($loop->index % 6)] }}"></div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Era Chart --}}
        <x-filament::section heading="Era Breakdown">
            <div class="space-y-2">
                @foreach($eraCounts as $label => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label ?: 'Unknown' }}</span>
                        <span class="text-sm font-bold">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="h-3 rounded-full" style="width: {{ $totalPrisoners > 0 ? round(($count / $totalPrisoners) * 100) : 0 }}%; background-color: {{ ['#825af9', '#35d9c3', '#4375ff', '#6d3dbf', '#2cb1a1', '#3658d4'][($loop->index % 6)] }}"></div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>

    {{-- Detailed Breakdowns --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Ideologies --}}
        <x-filament::section heading="Ideologies">
            @if(count($ideologyCounts) > 0)
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($ideologyCounts as $label => $count)
                        <div class="flex items-center justify-between py-1">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            <span class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-400/10 px-2 py-1 text-xs font-medium text-primary-700 dark:text-primary-400 ring-1 ring-inset ring-primary-600/20">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">No data yet</p>
            @endif
        </x-filament::section>

        {{-- Affiliations --}}
        <x-filament::section heading="Affiliations">
            @if(count($affiliationCounts) > 0)
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($affiliationCounts as $label => $count)
                        <div class="flex items-center justify-between py-1">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            <span class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-400/10 px-2 py-1 text-xs font-medium text-primary-700 dark:text-primary-400 ring-1 ring-inset ring-primary-600/20">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">No data yet</p>
            @endif
        </x-filament::section>
    </div>

    {{-- State Distribution --}}
    <x-filament::section heading="State Distribution">
        @if(count($stateCounts) > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2">
                @foreach($stateCounts as $label => $count)
                    <div class="text-center p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="text-lg font-bold text-primary-600">{{ $count }}</div>
                        <div class="text-xs text-gray-500">{{ $label ?: 'Unknown' }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">No data yet</p>
        @endif
    </x-filament::section>
</x-filament-panels::page>
