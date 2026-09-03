<div class="bg-gray-100"
     style="background-image: url('{{appearanceSetting()->getLoginBackground() ? getStorageUrl(appearanceSetting()->getLoginBackground()) : null}}'); background-size: cover">

    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white lg:rounded-lg shadow-xl w-[1200px] relative">

            <div class="flex justify-between items-center gap-5 h-[650px]">
                <div class="left-side w-full lg:w-1/2 h-full">
                    <div class="left-side-content px-10 lg:px-28 py-10 space-y-14">
                        <div class="logo text-center mb-6">
                            <img class="w-[300px] inline-block"
                                 src="{{appearanceSetting()->getLoginLogo() ? getStorageUrl(appearanceSetting()->getLoginLogo()) : asset('vendor/crudbooster/themes/assets/images/logo-cb-color.png')}}">
                        </div>

                        <div class="form-area">
                            <h2 class="text-2xl font-bold mb-1 text-gray-500 text-center poppins-light">
                                {{appearanceSetting()->getWelcomeText()}}</h2>
                            <p class="text-sm text-gray-400 text-center mb-3 poppins-extralight">
                                {{appearanceSetting()->getWelcomeSubText()}}</p>

                            @if(config('cb.demo_mode'))
                                <div class="alert alert-info">
                                    <strong>DEMO MODE</strong><br/>You can login with these bellow demo account.
                                </div>
                            @endif

                            <form wire:submit.prevent="submit">
                                <div class="mb-4 space-y-2">
                                    <label for="email"
                                           class="block text-gray-500">{{__('cb/auth.login_page.email')}}</label>
                                    <input type="email" id="email" placeholder="E.g: email@example.com"
                                           wire:model="email"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none text-gray-400"
                                           required>
                                    @error('email')
                                    <div class="my-1 block w-full text-red-400 text-sm">{{$message}}</div>@enderror
                                </div>
                                <div class="mb-6 space-y-2">
                                    <label for="password"
                                           class="block text-gray-500">{{__('cb/auth.login_page.password')}}</label>
                                    <div class="relative" x-data="{ show: false }">
                                        <input :type="show ? 'text' : 'password'" id="password" wire:model="password"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none text-gray-400"
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
                                    @error('password')
                                    <div class="my-1 block w-full text-red-400 text-sm">{{$message}}</div>@enderror
                                </div>
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="remember" wire:model="remember"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="remember"
                                               class="ml-2 block text-gray-500">{{__('cb/auth.login_page.remember_me')}}</label>
                                    </div>
                                    @if(securitySetting()->getLoginForgotEnabled() ?? true)
                                        <a href="{{getCmsUrl('auth/forgot')}}" wire:navigate
                                           class="text-sm text-sky-600 hover:underline">{{__('cb/auth.login_page.forgot_password')}}</a>
                                    @endif
                                </div>
                                <button class="btn btn-primary w-full mt-4" type="submit">
                                    <span wire:loading>{!! \CrudBooster\Components\Icon\Icon::SPIN !!}</span>
                                    <span class="text-lg">{{__('cb/auth.login_page.login')}}</span>
                                </button>
                            </form>
                        </div>

                        <div class="footer text-xs text-gray-500 text-center">
                            @include('cb.auth::auth-footer')
                        </div>
                    </div>
                </div>
                @if(appearanceSetting()->getLoginSplash())
                    <div class="right-side hidden lg:block w-1/2 h-full">
                        <img
                            src="{{ appearanceSetting()->getLoginSplash() ? getStorageUrl(appearanceSetting()->getLoginSplash()) : null }}"
                            class="w-full h-full object-cover rounded-r-lg">
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
