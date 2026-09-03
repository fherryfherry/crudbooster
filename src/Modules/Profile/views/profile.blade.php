@php use CrudBooster\Components\Icon\Icon; @endphp
<div>
    <div class="bg-gray-900 w-full md:w-32"></div>
    <!-- header Profile -->
    <h1 class="text-2xl font-bold block border-b pb-4 my-4 dark:border-gray-600">
        <div class="flex items-center gap-1">
            {!! Icon::USER !!} <span class="dark:text-gray-200">Profile</span>
        </div>
    </h1>
    <div class="flex flex-row items-start gap-5">
        <div class="w-[450px] bg-white text-gray-800 dark:bg-gray-800 dark:text-gray-200 shadow shadow-gray-300 dark:shadow-gray-700 rounded-md p-5">
            <div class="flex relative justify-center">
                <div class="inline-block relative">
                    @if($photo)
                    <img src="{{ $photo->temporaryUrl() }}" alt="{{ $name }}" class="w-36 h-36 rounded-full shadow-lg">
                    @else
                    <img src="{{ $user->photo ? getStorageUrl($user->photo) : asset(config('cb.default_avatar')) }}"
                        alt="{{ $user->name }}" class="w-36 h-36 thumbnail">
                    @endif
                    <div title="Change profile picture" onclick="document.querySelector('input[type=file]').click()"
                        class="absolute bottom-4 right-1 bg-sky-600 text-white p-2 rounded-full cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="text-center mt-6">
                <h2 class="text-lg font-semibold">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->position }}</p>
            </div>
        </div>
        <div class="w-full flex flex-col space-y-5">
            <div class="panel dark:bg-gray-800 dark:text-gray-200">
                <div class="panel-header dark:border-gray-600">
                    <div class="panel-title">
                        <h2>Detail Info</h2>
                    </div>
                </div>
                <div class="panel-content">
                    <form method="POST" wire:submit.prevent="update">
                        @csrf
                        <input type="file" wire:model="photo" class="hidden">

                        <div class="form-group">
                            <label for="name" class="dark:text-gray-200">Name</label>
                            <input type="text" id="name" wire:model="name" class="form-control dark:bg-gray-700 dark:text-gray-200">
                            @error('name')
                            <div class="form-error dark:text-red-400">{{$message}}</div>@enderror
                        </div>
                        <div class="form-group flex space-x-4">
                            <div class="w-1/2">
                                <label for="email" class="dark:text-gray-200">Email Address</label>
                                <input type="text" id="email" wire:model="email" class="form-control dark:bg-gray-700 dark:text-gray-200">
                                @error('email')
                                <div class="form-error dark:text-red-400">{{$message}}</div>@enderror
                            </div>
                            <div class="w-1/2">
                                <label for="phone" class="dark:text-gray-200">Phone</label>
                                <input type="text" id="phone" wire:model="phone" class="form-control dark:bg-gray-700 dark:text-gray-200">
                                @error('phone')
                                <div class="form-error dark:text-red-400">{{$message}}</div>@enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="position" class="dark:text-gray-200">Position</label>
                            <input type="text" id="position" wire:model="position" class="form-control dark:bg-gray-700 dark:text-gray-200">
                            @error('position')
                            <div class="form-error dark:text-red-400">{{$message}}</div>@enderror
                        </div>

                        <div class="w-full">
                            <div class="flex justify-end space-x-2">
                                <button class="btn btn-primary dark:btn-dark" type="submit">Update Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="panel dark:bg-gray-800 dark:text-gray-200">
                <div class="panel-header dark:border-gray-600">
                    <div class="panel-title">
                        <h2>Change Password</h2>
                    </div>
                </div>
                <div class="panel-content">
                    <form method="POST" wire:submit.prevent="changePassword">
                        @csrf
                        <div class="form-group">
                            <label for="old_password" class="dark:text-gray-200">Current Password</label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" id="old_password" wire:model="old_password"
                                       class="form-control dark:bg-gray-700 dark:text-gray-200" placeholder="Enter your current password"
                                       required>
                                <span x-cloak class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400"
                                      @click="show = !show">
                                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>

                                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </span>
                            </div>
                            @error('old_password')
                            <div class="form-error dark:text-red-400">{{$message}}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="password" class="dark:text-gray-200">New Password</label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" required placeholder="Enter new password" id="new_password"
                                       wire:model="password" class="form-control dark:bg-gray-700 dark:text-gray-200">
                                <span x-cloak class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400"
                                      @click="show = !show">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </span>
                            </div>
                            @error('password')
                            <div class="form-error dark:text-red-400">{{$message}}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation" class="dark:text-gray-200">Confirm Password</label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" required placeholder="Enter password again"
                                       id="password_confirmation" wire:model="password_confirmation" class="form-control dark:bg-gray-700 dark:text-gray-200">
                                <span x-cloak class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400"
                                      @click="show = !show">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </span>
                            </div>
                            @error('password_confirmation')
                            <div class="form-error dark:text-red-400">{{$message}}</div>@enderror
                        </div>

                        <div class="w-full">
                            <div class="flex justify-end space-x-2">
                                <button class="btn btn-primary dark:btn-dark" type="submit">Change Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
