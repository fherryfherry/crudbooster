<div x-cloak>
    @if($message)
        <div @click="$dispatch('closeAlertMessage')" class="fixed top-3 right-3 px-3 w-min-[350px] min-h-[50px] z-10">
            <div id="alert-message" class="alert flex justify-center items-center cursor-pointer alert-{{ $type ?? 'info' }}">
                <div class="alert-content flex justify-start gap-1 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor"
                         class="size-5 inline-block mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5"/>
                    </svg>
                    {{ $message }}
                </div>
            </div>
        </div>
    @endif
</div>

