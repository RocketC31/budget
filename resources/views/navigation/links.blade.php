<ul class="hidden w-full lg:flex">
    <li class="flex w-1/5">
        <a class="m-auto flex items-center" href="{{ route('index') }}" {!! (Request::path() == 'dashboard') ? 'class="active"' : '' !!}><i class="fas fa-home fa-sm color-blue"></i> <span class="md:flex ml-05">{{ __('general.dashboard') }}</span></a>
    </li>
    <li class="flex w-1/5">
        <a class="m-auto flex items-center" href="{{ route('transactions.index') }}" {!! (Request::path() == 'transactions') ? 'class="active"' : '' !!}><i class="fas fa-exchange-alt fa-sm color-green"></i> <span class="md:flex ml-05">{{ __('models.transactions') }}</span></a>
    </li>
    <li class="flex w-1/5">
        <a class="m-auto flex items-center" href="{{ route('budgets.index') }}" {!! (Request::path() == 'budgets') ? 'class="active"' : '' !!}><i class="fas fa-wallet fa-sm color-red"></i> <span class="md:flex ml-05">{{ __('models.budgets') }}</span></a>
    </li>
    <li class="flex w-1/5">
        <a class="m-auto flex items-center" href="{{ route('tags.index') }}" {!! (Request::path() == 'tags') ? 'class="active"' : '' !!}><i class="fas fa-tag fa-sm color-blue"></i> <span class="md:flex ml-05">{{ __('models.tags') }}</span></a>
    </li>
    <li class="flex w-1/5">
        <a class="m-auto flex items-center" href="{{ route('reports.index') }}" {!! (Request::path() == 'reports') ? 'class="active"' : '' !!}><i class="fas fa-chart-line fa-sm color-green"></i> <span class="md:flex ml-05">{{ __('pages.reports') }}</span></a>
    </li>
</ul>
<div class="flex lg:hidden" id="menu__mobile-btn">
    <div class="space-y-2">
        <span class="block w-8 h-0.5 bg-gray-600"></span>
        <span class="block w-8 h-0.5 bg-gray-600"></span>
        <span class="block w-8 h-0.5 bg-gray-600"></span>
    </div>
</div>
<div class="navbar-menu relative z-50" id="menu__mobile-panel">
    <div class="navbar-backdrop fixed inset-0 bg-gray-800 opacity-25"></div>
    <nav class="fixed top-0 left-0 bottom-0 flex flex-col sm:w-2/6 py-6 px-6 bg-white border-r overflow-y-auto w-full">
        <div class="flex items-center mb-8" id="menu__mobile-panel-btn">
            <button class="navbar-close text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div>
            <ul>
                <li class="mb-1">
                    <a class="block p-4 text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="{{ route('index') }}">{{ __('general.dashboard') }}</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="{{ route('transactions.index') }}">{{ __('models.transactions') }}</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="{{ route('budgets.index') }}">{{ __('models.budgets') }}</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="{{ route('tags.index') }}">{{ __('models.tags') }}</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="{{ route('reports.index') }}">{{ __('pages.reports') }}</a>
                </li>
            </ul>
        </div>
    </nav>
</div>
