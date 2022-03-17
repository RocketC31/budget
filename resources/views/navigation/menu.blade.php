<div class="navigation">
    <div class="wrapper">
        @include('navigation.links')
        <ul class="navigation__menu">
            <li class="">
                <dropdown>
                    <span slot="button" class="flex">
                        <img src="{{ Auth::user()->avatar }}" class="avatar mr-05" /> <i class="fas fa-caret-down fa-sm"></i>
                    </span>
                    <ul slot="menu" v-cloak>
                        <li>
                            <a href="{{ route('activities.index') }}">
                                {{ __('pages.activities') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('imports.index') }}">{{ __('models.imports') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index') }}">{{ __('pages.settings') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}">{{ __('pages.log_out') }}</a>
                        </li>
                        @if (Auth::user()->spaces->count() > 1)
                            <li>
                                <dropdown :direction="'left'">
                                    <a slot="button">
                                        {{ $selectedSpace->name }} <i class="fas fa-caret-down fa-sm"></i>
                                    </a>
                                    <ul slot="menu" v-cloak>
                                        @foreach (Auth::user()->spaces as $space)
                                            <li>
                                                <a href="{{ route('spaces.show', ['space' => $space->id])  }}" v-pre>{{ $space->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </dropdown>
                            </li>
                        @endif
                    </ul>
                </dropdown>
            </li>
        </ul>
    </div>
</div>
