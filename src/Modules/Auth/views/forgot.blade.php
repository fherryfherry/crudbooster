<div class="bg-gray-300">

    <div class="relative poppins-regular min-h-screen flex flex-col items-center justify-center bg-gray-100 space-y-6">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-sky-500"></div>
        <div class="relative bg-white p-8 lg:rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold mb-1 text-gray-500 text-center poppins-light">{{__('cb/auth.forgot_page.title')}}</h2>
            <p class="text-sm text-gray-400 text-center mb-6 poppins-extralight">{{__('cb/auth.forgot_page.subtitle')}}</p>
            <form wire:submit.prevent="submit">
                <div class="mb-4">
                    <label for="email" class="block text-gray-500">{{__('cb/auth.forgot_page.email')}}</label>
                    <input type="email" wire:loading.attr="disabled" placeholder="E.g. email@example.com" id="email"
                           wire:model="email"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none text-gray-400"
                           required>
                    @error('email')
                    <div class="my-1 block w-full text-red-400 text-sm">{{$message}}</div>@enderror
                </div>
                <div class="flex items-center justify-between mb-4">
                </div>
                <div class="flex text-center items-center justify-between gap-3">
                    <a class="btn btn-default w-1/5 whitespace-nowrap text-sm text-center"
                       title="{{__('cb/auth.forgot_page.login')}}" href="{{ getCmsUrl('auth/login') }}" wire:navigate>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-5 inline-block">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                    </a>
                    <button class="btn btn-primary w-full"
                            type="submit">{{__('cb/auth.forgot_page.send_reset_link')}}</button>
                </div>
            </form>
        </div>
        @include('cb.auth::auth-footer')
    </div>
</div>
