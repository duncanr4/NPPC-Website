<x-filament-panels::page>
    <div x-data="pageOrder(@js($parents))" class="space-y-6">

        {{-- Instructions --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Drag and drop to reorder pages. Parent pages control the top-level navigation order. Child pages control the dropdown menu order within each parent. Changes are saved automatically when you drop.
            </p>
        </div>

        {{-- Parent pages sortable list --}}
        <div x-ref="parentList" class="space-y-3">
            <template x-for="parent in parents" :key="parent.id">
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden"
                     :data-id="parent.id">

                    {{-- Parent row --}}
                    <div class="flex items-center gap-3 px-4 py-3 cursor-grab active:cursor-grabbing parent-handle">
                        {{-- Drag handle --}}
                        <div class="text-gray-400 dark:text-gray-500 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M4 8h16M4 16h16"/>
                            </svg>
                        </div>

                        {{-- Title & slug --}}
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-950 dark:text-white text-sm" x-text="parent.title"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="'/' + parent.slug"></div>
                        </div>

                        {{-- Nav badge --}}
                        <template x-if="parent.show_in_nav">
                            <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                                In Nav
                            </span>
                        </template>
                        <template x-if="!parent.show_in_nav">
                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                                Hidden
                            </span>
                        </template>

                        {{-- Children count --}}
                        <template x-if="parent.children.length > 0">
                            <span class="text-xs text-gray-400 dark:text-gray-500" x-text="parent.children.length + ' children'"></span>
                        </template>

                        {{-- Expand toggle --}}
                        <template x-if="parent.children.length > 0">
                            <button type="button"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded transition no-drag"
                                    @click.stop="toggleExpand(parent.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="{ 'rotate-180': expanded[parent.id] }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </template>
                    </div>

                    {{-- Children list --}}
                    <template x-if="parent.children.length > 0">
                        <div x-show="expanded[parent.id]" x-collapse>
                            <div class="border-t border-gray-100 dark:border-white/5">
                                <div :x-ref="'children-' + parent.id"
                                     :data-parent-id="parent.id"
                                     class="children-list divide-y divide-gray-50 dark:divide-white/5">
                                    <template x-for="child in parent.children" :key="child.id">
                                        <div class="flex items-center gap-3 px-4 py-2.5 pl-12 bg-gray-50/50 dark:bg-white/[0.02] cursor-grab active:cursor-grabbing"
                                             :data-id="child.id">
                                            {{-- Drag handle --}}
                                            <div class="text-gray-300 dark:text-gray-600 flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path d="M4 8h16M4 16h16"/>
                                                </svg>
                                            </div>

                                            {{-- Title & slug --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm text-gray-700 dark:text-gray-300" x-text="child.title"></div>
                                                <div class="text-xs text-gray-400 dark:text-gray-500" x-text="'/' + child.slug"></div>
                                            </div>

                                            {{-- Nav badge --}}
                                            <template x-if="child.show_in_nav">
                                                <span class="inline-flex items-center rounded-md bg-primary-50 px-1.5 py-0.5 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                                                    In Nav
                                                </span>
                                            </template>
                                            <template x-if="!child.show_in_nav">
                                                <span class="inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                                                    Hidden
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pageOrder', (initialParents) => ({
                parents: initialParents,
                expanded: {},
                parentSortable: null,
                childSortables: {},

                init() {
                    // Expand parents that have children by default
                    this.parents.forEach(p => {
                        if (p.children.length > 0) {
                            this.expanded[p.id] = true;
                        }
                    });

                    this.$nextTick(() => {
                        this.initParentSortable();
                        this.initChildSortables();
                    });

                    // Re-init child sortables when Livewire updates
                    Livewire.hook('morph.updated', () => {
                        this.$nextTick(() => {
                            this.initChildSortables();
                        });
                    });
                },

                toggleExpand(parentId) {
                    this.expanded[parentId] = !this.expanded[parentId];
                    if (this.expanded[parentId]) {
                        this.$nextTick(() => this.initChildSortables());
                    }
                },

                initParentSortable() {
                    const el = this.$refs.parentList;
                    if (!el || this.parentSortable) return;

                    this.parentSortable = new Sortable(el, {
                        animation: 200,
                        handle: '.parent-handle',
                        filter: '.no-drag',
                        preventOnFilter: false,
                        ghostClass: 'opacity-30',
                        dragClass: 'shadow-lg',
                        onEnd: () => {
                            const ids = Array.from(el.children)
                                .filter(child => child.dataset && child.dataset.id)
                                .map(child => child.dataset.id);
                            this.$wire.reorderParents(ids);
                        }
                    });
                },

                initChildSortables() {
                    document.querySelectorAll('.children-list').forEach(list => {
                        const parentId = list.dataset.parentId;
                        if (!parentId) return;

                        // Destroy existing instance if any
                        if (this.childSortables[parentId]) {
                            this.childSortables[parentId].destroy();
                        }

                        this.childSortables[parentId] = new Sortable(list, {
                            animation: 200,
                            ghostClass: 'opacity-30',
                            dragClass: 'shadow-lg',
                            onEnd: () => {
                                const ids = Array.from(list.children)
                                    .filter(child => child.dataset && child.dataset.id)
                                    .map(child => child.dataset.id);
                                this.$wire.reorderChildren(parentId, ids);
                            }
                        });
                    });
                }
            }));
        });
    </script>
</x-filament-panels::page>
