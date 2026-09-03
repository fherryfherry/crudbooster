<div x-data="{studioSidebar: !sidebar, showTitleWizard: @js(empty($data['name'])), titleInput: ''}" class="space-y-2">
    <!-- Title Wizard Modal -->
    <div x-show="showTitleWizard" style="z-index:1000;" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 w-full max-w-md flex flex-col items-center">
            <h2 class="text-xl font-bold mb-2 text-gray-700 dark:text-gray-200">Page Title Required</h2>
            <p class="mb-4 text-gray-500 dark:text-gray-300 text-center">Please enter a title for your page before editing.</p>
            <input x-ref="titleInput" x-model="titleInput" type="text" class="form-control w-full mb-4 text-lg" placeholder="Enter page title..." @keydown.enter.prevent="if(titleInput.trim().length > 0){ $wire.set('data.name', titleInput); showTitleWizard = false; }">
            <button class="btn btn-primary w-full" :disabled="titleInput.trim().length === 0" @click="if(titleInput.trim().length > 0){ $wire.set('data.name', titleInput); showTitleWizard = false; }">Continue</button>
        </div>
    </div>
    <!-- End Title Wizard Modal -->
    @include('cb.page-builder::page_top')

    <div class="md:hidden lg:hidden">
        <div
            class="flex flex-col gap-2 justify-center items-center text-center my-auto h-80 border border-dashed border-gray-400 dark:border-gray-600">
            <div class="italic text-gray-500 dark:text-gray-300">
                Please use desktop browser for<br/> better experience.
            </div>
            <a href="{{getCmsUrl('page-builder')}}" class="btn btn-outline-light inline-block dark:btn-outline-dark"
               wire:navigate>Go back</a>
        </div>
    </div>

    <div class="hidden lg:block">
        <!-- Floating Button-->
        @if(isset($data['name']))
        <button
            @click="studioSidebar = !studioSidebar; sidebar = !sidebar; document.cookie = 'sidebar=' + sidebar + '; path=/; max-age=' + (7 * 24 * 60 * 60);"
            class="fixed bottom-4 right-4 bg-sky-500 text-white p-4 rounded-full shadow-lg hover:bg-sky-600 dark:bg-sky-700 dark:hover:bg-sky-800 animate-bounce hover:animate-pulse">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-8" fill="currentColor">
                <path
                    d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z"/>
                <path
                    d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z"/>
            </svg>
        </button>
        @endif
        <!-- End of Floating Button-->

        <!-- Header Page Area -->
        <div class="flex justify-between items-center">
            <div class="name w-full">
                <input type="text" autofocus placeholder="E.g: Enter Title Here"
                       wire:model.live.debounce.600ms="data.name"
                       class="py-4 pl-1 text-2xl font-bold text-gray-400 border-b border-gray-300 w-full bg-transparent outline-0">
            </div>
            <div class="flex justify-end gap-1 w-1/3">
                <button class="btn btn-info" wire:click="preview" @disabled(!$id)>
                    <div class="flex justify-start items-center gap-1 text-white">
                        {!! \CrudBooster\Components\Icon\Icon::EYE !!}
                        <span>Preview</span>
                    </div>
                </button>
            </div>
        </div>
        <!-- End of Header Page Area -->

        <div x-data="pageArea()">
            <!-- Alert Message -->
            <div x-show="alertMessage" class="fixed top-10 w-full min-h-[50px] flex justify-center z-40">
                <div id="alert" @click="alertMessage = null"
                     class="alert flex justify-center items-center cursor-pointer">
                    <div class="alert-content">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor"
                             class="size-5 inline-block mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5"/>
                        </svg>
                        <span x-text="alertMessage"></span>
                    </div>
                </div>
            </div>
            <!-- End Alert Message -->

            <!-- Modal Config-->
            <div x-show="modal" class="modal dark:bg-gray-800 dark:text-white">
                <div class="relative" @click.away="closeModal">
                    <div class="modal-content min-h-[250px] md:w-[700px] sm:w-full dark:bg-gray-700">
                        <div class="modal-title border-b border-gray-300 pb-3 dark:border-gray-600">
                            <h3>{{$configElementTitle}}</h3>
                            <div class="text-sm font-normal text-gray-500 dark:text-gray-300">Element Configuration
                            </div>
                        </div>
                        <div class="min-h-[250px]">
                            @if($configElementComponent)
                                @livewire($configElementComponent, ['id'=>$id, 'rowIndex'=>$rowIndex,
                                'colIndex'=>$colIndex])
                            @else
                                <div class="animate-pulse flex space-x-4">
                                    <div class="flex-1 space-y-4 py-1">
                                        <div class="h-4 bg-gray-200 rounded w-3/4 dark:bg-gray-600"></div>
                                        <div class="space-y-2">
                                            <div class="h-4 bg-gray-200 rounded dark:bg-gray-600"></div>
                                            <div class="h-4 bg-gray-200 rounded w-5/6 dark:bg-gray-600"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal Config-->

            <div class="flex flex-grow gap-4 h-screen">
                <div class="w-full" id="grid-wrapper">
                    @if(!$grid)
                        <div id="default-page-area" @dragover="pickOver($event)" @drop="pickDrop($event)"
                             class="bg-gray-200 dark:bg-gray-700 border-4 border-dotted rounded-md border-gray-300 dark:border-gray-600 min-h-[400px] flex justify-center items-center text-center text-gray-300 dark:text-gray-400 font-bold h-screen">
                            Page Area
                        </div>
                    @else
                        <div id="page-area-grid" class="h-screen space-y-4" @dragover="pickOver($event)"
                             @drop="pickDrop($event)">
                            @foreach($grid as $rowIndex=>$columns)
                                <div id="grid-{{$rowIndex}}" data-row-index="{{$rowIndex}}"
                                     class="flex flex-grow justify-between gap-4">
                                    @foreach($columns as $colIndex=>$column)
                                        <div
                                            class="bg-gray-200 dark:bg-gray-700 hover:shadow-md border-4 border-dotted rounded-md border-gray-300 dark:border-gray-600 h-[150px] w-full cursor-grab active:cursor-grabbing">
                                            <div id="column-{{$rowIndex}}-{{$colIndex}}"
                                                 data-placeholder="{{$column['content']['placeholder']}}"
                                                 data-row-index="{{$rowIndex}}" data-col-index="{{$colIndex}}"
                                                 draggable="true"
                                                 @dragstart="sortColumnStart($event)"
                                                 @dragover="sortColumnOver($event)"
                                                 @drop="sortColumnDrop($event)"
                                                 class="relative text-center h-full w-full flex items-center justify-center">
                                                <div class="text-gray-400 absolute top-1 right-1 hover:text-gray-600">
                                                    <div class="flex justify-between items-center gap-2">
                                                        <a title="Add new right column" href="javascript:"
                                                           @click="addRightColumn({{$rowIndex}},{{$colIndex}})">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                 viewBox="0 0 24 24" stroke-width="1"
                                                                 stroke="currentColor"
                                                                 class="size-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125Z"/>
                                                            </svg>
                                                        </a>
                                                        <a title="Delete this element" href="javascript:"
                                                           @click="deleteConfirm({{$rowIndex}},{{$colIndex}})">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                 viewBox="0 0 24 24" stroke-width="1"
                                                                 stroke="currentColor"
                                                                 class="size-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                            </svg>
                                                        </a>
                                                        @if(isset($column['content']['type']))
                                                            <a title="Edit this element" href="javascript:"
                                                               wire:click="editElement({{$rowIndex}},{{$colIndex}})"
                                                               @click="editElement">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                     viewBox="0 0 24 24"
                                                                     stroke-width="1" stroke="currentColor"
                                                                     class="size-5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                                                </svg>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(isset($column['content']['type']))
                                                    <div>
                                                        <div
                                                            class="text-gray-400 text-2xl">{{$column['content']['placeholder']}}</div>
                                                        <div
                                                            class="text-sky-400 text-xl block">{{$column['content']['config']['title']??'Unknown'}}</div>
                                                    </div>
                                                @else
                                                    <span
                                                        class="text-gray-300 text-2xl">{{$column['content']['placeholder']}}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div x-show="studioSidebar" class="studio-sidebar w-[350px] h-full"
                     x-transition:enter="transition ease-out duration-200 transform"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                >
                    <div
                        class="bg-white p-4 rounded-lg shadow-md h-full overflow-auto dark:bg-gray-800 dark:text-white">
                        <div class="space-y-2">
                            @foreach($tools as $tool)
                                <div class="p-2">
                                    <h4 class="font-semibold text-gray-700">{{$tool['group']}}</h4>
                                    <ul class="mt-2 space-y-2">
                                        @foreach($tool['tools'] as $item)
                                            <li @if($item['is_active']) title="Drag this element to work area" draggable="true" @dragstart="pickStart($event)"
                                                @endif
                                                class="{{$item['is_active'] ? "tool-item-active" : "tool-item-inactive"}} w-full overflow-auto overflow-ellipsis"
                                                data-type="{{$item['type']}}" data-type-label="{{$item['name']}}">
                                                {!! $item['icon'] !!}
                                                <span>{{$item['name']}}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Confirm Dialog -->
                <div x-show="confirmTitle"
                     class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-gray-900 bg-opacity-10 z-20 dark:bg-gray-800 dark:bg-opacity-50">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-1/3 dark:bg-gray-700">
                        <h2 class="text-lg font-semibold border-b pb-3 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                 class="size-6 inline-block align-middle">
                                <path fill-rule="evenodd"
                                      d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <span class="align-middle" x-text="confirmTitle"></span>
                        </h2>
                        <div class="mt-3 dark:text-gray-300" x-text="confirmMessage"></div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <button type="button" @click="doDeleteElement" class="btn btn-danger">Ok</button>
                            <button type="button" @click="confirmTitle=''"
                                    class="btn btn-default dark:bg-gray-600 dark:text-gray-300">Cancel
                            </button>
                        </div>
                    </div>
                </div>
                <!-- End Confirm Dialog -->
            </div>
        </div>
    </div>
</div> <!-- end of page studio -->

<script>
    function pageArea() {
        return {
            confirmTitle: '',
            confirmMessage: '',
            type: null,
            typeLabel: null,
            rowIndex: 0,
            colIndex: 0,
            elementId: null,
            alertMessage: '',
            alertType: 'success',
            modal: false,
            modalTitle: '',
            init() {
                Livewire.on('saved', (event) => {
                    this.showAlert(event.message, event.type);
                    this.closeModal();
                    Livewire.dispatch('elementSaved');
                });
            },
            closeModal() {
                this.modal = false;
            @this.clearEditElement()
                ;
            },
            showAlert(message, type, duration = 2000) {
                this.alertMessage = message;
                this.alertType = type;

                let alert = document.getElementById('alert');
                alert.classList.add('alert-' + this.alertType);

                anime({
                    targets: '#alert',
                    translateY: [-100, 0],
                    endDelay: duration,
                    direction: 'alternate'
                });
            },
            alertElementSuccessSaved() {
                this.showAlert('Element has been saved!', 'success', 500);
            },
            showConfirm(title, message) {
                this.confirmTitle = title;
                this.confirmMessage = message;
            },
            pickStart(event) {
                this.elementId = null;
                this.type = event.target.getAttribute('data-type');
                this.typeLabel = event.target.getAttribute('data-type-label');
            },
            pickOver(event) {
                event.preventDefault();
                // add class border-dashed border-4 border-red-300
                if (this.elementId && this.elementId.includes('column-')) {
                    return;
                }

                // Prevent hover border on column if pick is grid
                if (this.type.includes('grid-') && event.target.id.includes('column-')) {
                    return;
                }

                if (this.type.includes("grid-") && !event.target.id.includes("column-")) {
                    event.target.classList.add('border-dashed', 'border-4', 'border-red-300');
                    event.target.addEventListener('dragleave', this.removeBorder);
                }

                if (!this.type.includes("grid-") && event.target.id.includes('column-')) {
                    event.target.classList.add('border-dashed', 'border-4', 'border-red-300');
                    event.target.addEventListener('dragleave', this.removeBorder);
                }
            },
            removeBorder(event) {
                event.target.classList.remove('border-dashed', 'border-4', 'border-red-300');
            },
            pickDrop(event) {
                event.preventDefault();
                // if elementId contain column-, then skip
                if (this.elementId && this.elementId.includes('column-')) {
                    return;
                }

                if (event.target.id.includes('column-')) {
                    let rowIndex = event.target.getAttribute('data-row-index');
                    let colIndex = event.target.getAttribute('data-col-index');
                @this.setContent(rowIndex, colIndex, this.type, this.typeLabel)
                }

                if (this.type.includes('grid-')) {
                @this.addColumn(this.type)
                }
            },

            sortColumnStart(event) {
                console.log("Sort column start!");
                this.type = null;
                this.typeLabel = null;
                this.elementId = event.target.id;
                this.rowIndex = event.target.getAttribute('data-row-index');
                this.colIndex = event.target.getAttribute('data-col-index');
            },
            sortColumnOver(event) {
                event.preventDefault();
                if (!this.type && event.target.id.includes('column-')) {
                    event.target.classList.add('border-dashed', 'border-4', 'border-red-300');
                    event.target.addEventListener('dragleave', this.removeBorder);
                }
            },
            sortColumnDrop(event) {
                if (this.type) return;
                let newRowIndex = event.target.getAttribute('data-row-index');
                let newColIndex = event.target.getAttribute('data-col-index');
                console.log("Sort column drop = " + event.target.getAttribute("data-placeholder"));
            @this.sortColumn(newRowIndex, newColIndex, this.rowIndex, this.colIndex)
            },
            deleteConfirm(rowIndex, colIndex) {
                this.rowIndex = rowIndex;
                this.colIndex = colIndex;
                this.showConfirm('Delete Element', 'Are you sure you want to delete this element?');
            },
            doDeleteElement() {
            @this.deleteColumn(this.rowIndex, this.colIndex)
                this.confirmTitle = '';
                this.confirmMessage = '';
                this.showAlert('Element has been deleted!', 'success');
            },
            addRightColumn(rowIndex, colIndex) {
                this.showAlert('New column has been added!', 'success');
            @this.addRightColumn(rowIndex, colIndex)
            },
            editElement() {
                this.modal = true;
            }
        }
    }
</script>
