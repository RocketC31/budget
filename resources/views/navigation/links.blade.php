<ul class="hidden w-full lg:flex">
    <li class="flex w-1/5">
        <a class="m-auto flex items-center {!! (Request::path() == 'dashboard') ? 'active' : '' !!}" href="{{ route('index') }}"><i class="fas fa-home fa-sm color-blue"></i> <span class="md:flex ml-05">{{ __('general.dashboard') }}</span></a>
    </li>
    <li class="flex w-1/5">
        <a class="m-auto flex items-center {!! (Request::path() == 'transactions') ? 'active' : '' !!}" href="{{ route('transactions.index') }}"><i class="fas fa-exchange-alt fa-sm color-green"></i> <span class="md:flex ml-05">{{ __('models.transactions') }}</span></a>
    </li>
    <li class="flex w-1/5">
        <a class="m-auto flex items-center {!! (Request::path() == 'budgets') ? 'active' : '' !!}" href="{{ route('budgets.index') }}"><i class="fas fa-wallet fa-sm color-red"></i> <span class="md:flex ml-05">{{ __('models.budgets') }}</span></a>
    </li>
    <li class="flex w-1/5">
        <a class="m-auto flex items-center {!! (Request::path() == 'tags') ? 'active' : '' !!}" href="{{ route('tags.index') }}"><i class="fas fa-tag fa-sm color-blue"></i> <span class="md:flex ml-05">{{ __('models.tags') }}</span></a>
    </li>
    <li class="flex w-1/5">
        <a class="m-auto flex items-center {!! (Request::path() == 'reports') ? 'active' : '' !!}" href="{{ route('reports.index') }}"><i class="fas fa-chart-line fa-sm color-green"></i> <span class="md:flex ml-05">{{ __('pages.reports') }}</span></a>
    </li>
</ul>
<div class="flex lg:hidden cursor-pointer" id="menu__mobile-btn">
    <div class="space-y-2">
        <span class="block w-8 h-0.5 bg-gray-600"></span>
        <span class="block w-8 h-0.5 bg-gray-600"></span>
        <span class="block w-8 h-0.5 bg-gray-600"></span>
    </div>
</div>
<div class="navbar-backdrop fixed inset-0 bg-gray-800" id="menu__mobile_backdrop"></div>
<div class="box navbar-menu z-50 fixed top-0 left-0 bottom-0 flex flex-col sm:w-2/6 py-6 px-6 bg-white border-r overflow-y-auto w-full" id="menu__mobile-panel">
    <nav>
        <div class="flex items-center mb-8 cursor-pointer" id="menu__mobile-panel-btn">
            <button class="p-4 navbar-close text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div>
            <ul>
                <li class="mb-1">
                    <a class="block p-4 text-gray-400 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 rounded {!! (Request::path() == 'dashboard') ? 'active' : '' !!}" href="{{ route('index') }}">{{ __('general.dashboard') }}</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-gray-400 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 rounded {!! (Request::path() == 'transactions') ? 'active' : '' !!}" href="{{ route('transactions.index') }}">{{ __('models.transactions') }}</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-gray-400 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 rounded {!! (Request::path() == 'budgets') ? 'active' : '' !!}" href="{{ route('budgets.index') }}">{{ __('models.budgets') }}</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-gray-400 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 rounded {!! (Request::path() == 'tags') ? 'active' : '' !!}" href="{{ route('tags.index') }}">{{ __('models.tags') }}</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-gray-400 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 rounded {!! (Request::path() == 'reports') ? 'active' : '' !!}" href="{{ route('reports.index') }}">{{ __('pages.reports') }}</a>
                </li>
            </ul>
        </div>
    </nav>
</div>
