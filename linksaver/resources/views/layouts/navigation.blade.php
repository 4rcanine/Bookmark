{{-- resources/views/layouts/navigation.blade.php --}}
{{-- Use $navClass passed from app.blade.php if needed --}}
<nav x-data="{ open: false }" class="{{ $navClass ?? '' }} bg-cozy-cream/70 border-b border-cozy-brown/30 backdrop-blur-sm sticky top-0 z-50"> {{-- Example: Semi-transparent cream, adjust as needed --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('bookmarks.index') }}" class="flex items-center gap-2 text-cozy-text font-semibold">
                         {{-- Use your App Logo Component or SVG --}}
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cozy-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"> <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /> </svg>
                        <span>LinkSaver+</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('bookmarks.index')" :active="request()->routeIs('bookmarks.*')" class="!text-cozy-text hover:!border-cozy-brown focus:!border-cozy-brown">
                        {{ __('Bookmarks') }}
                    </x-nav-link>
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="!text-cozy-text hover:!border-cozy-brown focus:!border-cozy-brown">
                        {{ __('Categories') }}
                    </x-nav-link>
                    {{-- Add other links like Resources if needed, matching target image --}}
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-cozy-text bg-transparent hover:text-cozy-brown focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    {{-- Dropdown Content Styling --}}
                    <x-slot name="content">
                         <div class="bg-cozy-cream rounded-md shadow-lg ring-1 ring-black ring-opacity-5 py-1">
                            <x-dropdown-link :href="route('profile.edit')" class="!text-cozy-text hover:!bg-cozy-cream">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                         onclick="event.preventDefault(); this.closest('form').submit();"
                                         class="!text-cozy-text hover:!bg-cozy-cream">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                 <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-cozy-text/80 hover:text-cozy-text hover:bg-cozy-cream/50 focus:outline-none focus:bg-cozy-cream/50 focus:text-cozy-text transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-cozy-cream border-t border-cozy-brown/30">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('bookmarks.index')" :active="request()->routeIs('bookmarks.*')" class="!text-cozy-text">
                {{ __('Bookmarks') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="!text-cozy-text">
                {{ __('Categories') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-cozy-brown/30">
            <div class="px-4">
                 <div class="font-medium text-base text-cozy-text">{{ Auth::user()->name }}</div>
                 <div class="font-medium text-sm text-cozy-text/80">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="!text-cozy-text">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                             class="!text-cozy-text">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>