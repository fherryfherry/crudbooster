<div class="bg-gray-300">

    <div class="relative poppins-regular min-h-screen flex flex-col items-center justify-center bg-gray-100 space-y-6">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-sky-500"></div>
        <div class="relative bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold mb-1 text-gray-500 text-center poppins-light">{{__('cb/auth.reset_password_page.title')}}</h2>
            <p class="text-sm text-gray-400 text-center mb-6 poppins-extralight">{{__('cb/auth.reset_password_page.subtitle')}}</p>
            @if($isValid)
            <form wire:submit.prevent="submit">
                <div class="mb-4">
                    <label for="password" class="block text-gray-500 text-sm">{{ __('cb/auth.reset_password_page.new_password') }}</label>
                    <input type="password" wire:loading.attr="disabled" id="password"
                           wire:model="password"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none text-gray-400"
                           required>
                    <div class="my-1 block w-full text-gray-400 text-sm">{{ __('cb/auth.reset_password_page.password_must_at_least') }}</div>
                    @error('password')
                    <div class="my-1 block w-full text-red-400 text-sm">{{$message}}</div>@enderror
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-500 text-sm">{{ __('cb/auth.reset_password_page.confirm_new_password') }}</label>
                    <input type="password" wire:loading.attr="disabled" id="password_confirmation"
                           wire:model="password_confirmation"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none text-gray-400"
                           required>
                    <div class="my-1 block w-full text-gray-400 text-sm">{{ __('cb/auth.reset_password_page.type_same_password') }}</div>
                    @error('password_confirmation')
                    <div class="my-1 block w-full text-red-400 text-sm">{{$message}}</div>@enderror
                </div>
                <div class="flex items-center justify-between mb-4"></div>
                <button class="btn btn-primary w-full" type="submit">{{ __('cb/auth.reset_password_page.change_password') }}</button>
            </form>
            @else
                <div class="alert alert-warning alert-simple">{!! __('cb/auth.reset_password_page.token_reset_invalid', ['url' => route('login')]) !!}</div>
            @endif
        </div>
        @include('cb.auth::auth-footer')
    </div>
</div>
