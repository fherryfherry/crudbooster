<div>
    @if(isset($confirmTitle))
        {!! confirmMessageTag($confirmTitle, $confirmMessage, $confirmAction, $confirmButtonText, $confirmButtonColor) !!}
    @endif
    <h1 class="text-2xl mb-10 flex justify-start items-center gap-2">{!! \CrudBooster\Components\Icon\Icon::IMAGE !!}
        Appearance</h1>
    <!-- frame General, with upload Favicon (png/jpg) with max size 16x16 (set as form help) -->
    <div class="frame">
        <div class="frame-title">
            General
        </div>
        <div class="frame-content">
            <form wire:submit.prevent="save">
                <div class="form-group">
                    <label>Favicon</label>
                    <div class="mb-2">
                        @if(isset($form['favicon']) && is_object($form['favicon']) && !is_string($form['favicon']))
                            <a href="javascript:"
                               @click="showPreviewImage('{{ $form['favicon']->temporaryUrl() }}')">
                                <img src="{{ $form['favicon']->temporaryUrl() }}"
                                     class="w-32 h-32 object-cover rounded-lg">
                            </a>
                        @endif
                        @if(isset($form['favicon']) && is_string($form['favicon']))
                            <a href="javascript:" @click="showPreviewImage('{{ getStorageUrl($form['favicon']) }}')">
                                <img src="{{ getStorageUrl($form['favicon']) }}"
                                     class="w-32 h-32 object-cover rounded-lg">
                            </a>
                            <button type="button" class="btn btn-outline-danger mt-2"
                                    wire:click="showConfirmMessage('Delete Confirm', 'Are you sure want to remove this file?', 'removeFile(\'favicon\')')">
                                Remove Image
                            </button>
                        @endif
                    </div>
                    <input type="file" class="form-control" accept=".jpg,.jpeg,.png" wire:model="form.favicon">
                    <div class="form-help">
                        Recommended size: 16x16, file format: PNG/JPG
                    </div>
                </div>
                <div class="flex flex-row justify-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
    <div class="frame">
        <div class="frame-title">
            Login Page
        </div>
        <div class="frame-content">
            <form wire:submit.prevent="save">
                <div class="form-group">
                    <label>Logo</label>
                    <div class="mb-2">
                        @if(isset($form['login_logo']) && is_object($form['login_logo']) && !is_string($form['login_logo']))
                            <a href="javascript:"
                               @click="showPreviewImage('{{ $form['login_logo']->temporaryUrl() }}')">
                                <img src="{{ $form['login_logo']->temporaryUrl() }}"
                                     class="w-48 h-32 object-cover rounded-lg">
                            </a>
                        @endif
                        @if(isset($form['login_logo']) && is_string($form['login_logo']))
                            <a href="javascript:" @click="showPreviewImage('{{ getStorageUrl($form['login_logo']) }}')">
                                <img src="{{ getStorageUrl($form['login_logo']) }}"
                                     class="w-52 max-h-44 object-cover rounded-lg">
                            </a>
                            <button type="button" class="btn btn-outline-danger mt-2"
                                    wire:click="showConfirmMessage('Delete Confirm', 'Are you sure want to remove this file?', 'removeFile(\'login_logo\')')">
                                Remove Image
                            </button>
                        @endif
                    </div>
                    <input type="file" class="form-control" accept=".jpg,.jpeg,.png" wire:model="form.login_logo">
                    <div class="form-help">
                        Recommended size: 200x50px, file format: PNG/JPG
                    </div>
                </div>
                <div class="form-group">
                    <label>Splash Image</label>
                    <div class="mb-2">
                        @if(isset($form['login_splash']) && is_object($form['login_splash']) && !is_string($form['login_splash']))
                            <a href="javascript:"
                               @click="showPreviewImage('{{ $form['login_splash']->temporaryUrl() }}')">
                                <img src="{{ $form['login_splash']->temporaryUrl() }}"
                                     class="w-32 h-32 object-cover rounded-lg">
                            </a>
                        @endif
                        @if(isset($form['login_splash']) && is_string($form['login_splash']))
                            <a href="javascript:"
                               @click="showPreviewImage('{{ getStorageUrl($form['login_splash']) }}')">
                                <img src="{{ getStorageUrl($form['login_splash']) }}"
                                     class="w-32 h-32 object-cover rounded-lg">
                            </a>
                            <button type="button" class="btn btn-outline-danger mt-2"
                                    wire:click="showConfirmMessage('Delete Confirm', 'Are you sure want to remove this file?', 'removeFile(\'login_splash\')')">
                                Remove Image
                            </button>
                        @endif
                    </div>
                    <input type="file" class="form-control" accept=".jpg,.jpeg,.png" wire:model="form.login_splash">
                    <div class="form-help">
                        Recommended size: 590x650, file format: PNG/JPG
                    </div>
                </div>
                <div class="form-group">
                    <label>Background Image</label>
                    <div class="mb-2">
                        @if(isset($form['login_background']) && is_object($form['login_background']) && !is_string($form['login_background']))
                            <a href="javascript:"
                               @click="showPreviewImage('{{ getStorageUrl($form['login_splash']) }}')">
                                <img src="{{ $form['login_background']->temporaryUrl() }}"
                                     class="w-32 h-32 object-cover rounded-lg">
                            </a>
                        @endif
                        @if(isset($form['login_background']) && is_string($form['login_background']))
                            <a href="javascript:"
                               @click="showPreviewImage('{{ getStorageUrl($form['login_background']) }}')">
                                <img src="{{ getStorageUrl($form['login_background']) }}"
                                     class="w-32 h-32 object-cover rounded-lg">
                            </a>
                            <button type="button" class="btn btn-outline-danger mt-2"
                                    wire:click="showConfirmMessage('Delete Confirm', 'Are you sure want to remove this file?', 'removeFile(\'login_background\')')">
                                Remove Image
                            </button>
                        @endif
                    </div>
                    <input type="file" class="form-control" accept=".jpg,.jpeg,.png" wire:model="form.login_background">
                    <div class="form-help">
                        Recommended size: 1920x1080, file format: PNG/JPG
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Welcome Text</label>
                    <input type="text" placeholder="E.g: Welcome To Awesome App" wire:model="form.login_welcome_text" class="form-control w-full">
                    <div class="form-help">
                        This is the text that will appear on the login page at heading
                    </div>

                    <input type="text" placeholder="E.g: Please login to continue" wire:model="form.login_welcome_sub_text" class="form-control w-full">
                    <div class="form-help">
                        This is the text that will appear on the login page at sub heading
                    </div>

                </div>
                <div class="form-group">
                    <label for="">Footer Text</label>
                    <input type="text" wire:model="form.login_footer_text" class="form-control w-full">
                    <div class="form-help">
                        This is the text that will appear on the footer of your application
                    </div>
                </div>
                <div class="flex flex-row justify-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
    <div class="frame">
        <div class="frame-title">Sidebar</div>
        <div class="frame-content">
            <form wire:submit.prevent="save">
                <div class="form-group">
                    <label>Logo</label>
                    <div class="mb-2">
                        @if(isset($form['sidebar_logo']) && is_object($form['sidebar_logo']) && !is_string($form['sidebar_logo']))
                            <a href="javascript:"
                               @click="showPreviewImage('{{ getStorageUrl($form['sidebar_logo']) }}')">
                                <img src="{{ $form['sidebar_logo']->temporaryUrl() }}"
                                     class="w-32 h-32 object-cover rounded-lg">
                            </a>
                        @endif
                        @if(isset($form['sidebar_logo']) && is_string($form['sidebar_logo']))
                            <a href="javascript:"
                               @click="showPreviewImage('{{ getStorageUrl($form['sidebar_logo']) }}')">
                                <img src="{{ getStorageUrl($form['sidebar_logo']) }}"
                                     class="w-32 h-32 object-cover rounded-lg">
                            </a>
                            <button type="button" class="btn btn-outline-danger mt-2"
                                    wire:click="showConfirmMessage('Delete Confirm', 'Are you sure want to remove this file?', 'removeFile(\'sidebar_logo\')')">
                                Remove Image
                            </button>
                        @endif
                    </div>
                    <input type="file" class="form-control" accept=".jpg,.jpeg,.png" wire:model="form.sidebar_logo">
                    <div class="form-help">
                        Recommended size: 160x40, file format: PNG/JPG
                    </div>
                </div>
                <div class="flex flex-row justify-end mt-4">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
