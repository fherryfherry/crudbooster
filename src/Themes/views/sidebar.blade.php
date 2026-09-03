<!-- Logo -->
<div x-cloak class="logo px-3">
    <a href="{{getCmsUrl()}}" class="text-center" wire:navigate>
        @if(appearanceSetting()->getSidebarLogo())
        <img
            src="{{appearanceSetting()->getSidebarLogo() ? getStorageUrl(appearanceSetting()->getSidebarLogo()) : null}}"
            alt="CRUDBooster"
            class="w-[160px] inline-block h-[40px]">
        @else
            <span class="text-2xl text-white">{{basicInfoSetting()->getAppName()}}</span>
        @endif
    </a>
</div>
@if($menuTagList = getMenuTags())
    @foreach($menuTagList as $menuTag)
        @php $menus = getMenus($menuTag) @endphp
        @if($menus && count($menus) > 0)
        <h2 class="section-title">{{$menuTag ?? 'Menu'}}</h2>
        <ul class="menu" >
            @foreach($menus as $menu)
                <li class="menu-item" x-data="{open: false}">
                    @if(isset($menu['child']) && count($menu['child']) > 0)
                        <a href="javascript:" @click="open = !open">
                            {!! \CrudBooster\Components\Icon\Icon::valueOf($menu['icon']) !!}
                            <span>{{$menu['name']}}</span>
                            <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor"
                                 class="size-4 inline-block ml-auto">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                            </svg>
                            <svg x-show="open" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor"
                                 class="size-4 inline-block ml-auto">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </a>
                        <ul x-show="open" x-transition class="ml-2 mt-4">
                            @foreach($menu['child'] as $child)
                                <li class="mb-3">
                                    <a href="{{$child->menu_url}}" @if($child->wire_navigation)
                                        wire:navigate
                                        @endif
                                    >
                                        {!! \CrudBooster\Components\Icon\Icon::valueOf($child->icon) !!}
                                        <span>{{$child->name}}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{$menu->menu_url}}" @if($menu->wire_navigation)
                            wire:navigate
                            @endif
                        >
                            {!! \CrudBooster\Components\Icon\Icon::valueOf($menu->icon) !!}
                            <span>{{$menu->name}}</span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
        @endif
    @endforeach
@endif
