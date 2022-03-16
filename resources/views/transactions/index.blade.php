@extends('layout')

@section('title', __('models.transactions'))

@section('body')
    <div class="wrapper my-3" id="transactions-page">
        <div class="row mb-3">
            <div class="row__column row__column--middle">
                <h2>{{ __('models.transactions') }}</h2>
            </div>
            <div class="row__column row__column--compact row__column--middle">
                <a href="{{ route('transactions.create') }}" class="button">{{ __('actions.create') }} {{ __('models.transactions') }}</a>
            </div>
        </div>
        <div class="row md:flex-row flex-col">
            <div class="row__column md:mr-3 mb-2 md:max-w-xs w-full">
                <div class="box">
                    <div class="box__section">
                        <div class="mb-2">
                            <a href="{{ route('transactions.index') }}">{{ __('actions.reset') }}</a>
                        </div>
                        <span>{{ __('activities.tag.filter') }}</span>
                        @foreach ($tags as $tag)
                            <div class="mt-1 ml-1">
                                <a href="{{ route('transactions.index', [ 'filterBy' => ['tag' => $tag->id], "monthIndex" => $currentMonthIndex]) }}" v-pre>{{ $tag->name }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row__column w-full">
                <h2 class="mb-2">
                    <a href="{{ route('transactions.index', ['monthIndex' => ($currentMonthIndex - 1) ]) }}"><i class="fa fa-chevron-left"></i></a>
                    {{ __('calendar.months.' . $month) }}, {{ $year }}
                    <a href="{{ route('transactions.index', ['monthIndex' => ($currentMonthIndex + 1) ]) }}"><i class="fa fa-chevron-right"></i></a>
                </h2>
                <ul class="flex flex-wrap border-b border-gray-200 dark:border-gray-700 mb-2" id="transaction-tabs">
                    <li>
                        <a href="#" onclick="changeTab(this, 'list')" aria-current="page" class="transaction-link inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg active">
                            {{ __('fields.list') }}
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="changeTab(this, 'chart')" class="transaction-link inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg">
                            {{ __('fields.chart') }}
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="changeTab(this, 'tags')" class="transaction-link inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg">
                            {{ __('models.tags') }}
                        </a>
                    </li>
                </ul>
                @if ($transactions)
                    <div class="box transaction-block overflow-auto" id="transaction-list">
                        <table class="w-full whitespace-nowrap">
                            <tbody>
                                @foreach ($transactions as $key => $transaction)
                                    <tr tabindex="0" class="focus:outline-none h-16 border-y border-gray-100 dark:border-gray-700 rounded">
                                        <td class="px-1">
                                            <div class="flex items-center pl-5">
                                                <p class="text-base font-medium leading-none text-gray-700 dark:text-gray-500 mr-2">
                                                    {{ $transaction->description }}
                                                    <br> <span class="text-sm text-gray-400">{{ $transaction->happened_on->format('d') }} {{ __('calendar.months.' . $month) }}, {{ $year }}</span>
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-3 text-center">
                                            <div class="flex items-center dark:text-gray-500">
                                                @if ($transaction->tag)
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M9.16667 2.5L16.6667 10C17.0911 10.4745 17.0911 11.1922 16.6667 11.6667L11.6667 16.6667C11.1922 17.0911 10.4745 17.0911 10 16.6667L2.5 9.16667V5.83333C2.5 3.99238 3.99238 2.5 5.83333 2.5H9.16667" stroke="#{{ $transaction->tag->color }}" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <circle cx="7.50004" cy="7.49967" r="1.66667" stroke="#{{ $transaction->tag->color }}" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></circle>
                                                    </svg>
                                                    <p class="ml-1">{{ $transaction->tag->name }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-3 w-10 text-center dark:text-gray-500">
                                            @if ($transaction->recurring_id)
                                                <i class="fas fa-recycle"></i>
                                            @endif
                                        </td>
                                        <td class="px-3 text-center">
                                            <div class="py-3 px-3 text-sm focus:outline-none leading-none {{ get_class($transaction) == 'App\Models\Earning' ? 'text-green-700 bg-green-300 dark:text-green-200 dark:bg-green-600' : 'text-red-800 bg-red-400 dark:text-red-200 dark:bg-red-600' }} rounded">
                                                {!! $currency !!} {{ $transaction->formatted_amount }}
                                            </div>
                                        </td>
                                        <td class="px-3 text-center">
                                            <div class="flex items-center justify-around focus:ring-2 focus:ring-offset-2 focus:ring-red-300 text-sm leading-none text-gray-600 dark:text-gray-200 dark:bg-gray-600 bg-gray-100 rounded hover:bg-gray-200 focus:outline-none">
                                                <a class="p-3" href="{{ route(get_class($transaction) === 'App\Models\Earning' ? 'earnings.show' : 'spendings.show', [ $transaction->id ]) }}">
                                                    <i class="fas fa-info-circle fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                                                </a>
                                                <a class="p-3" href="{{ route(get_class($transaction) === 'App\Models\Earning' ? 'earnings.edit' : 'spendings.edit', [ $transaction->id ]) }}">
                                                    <i class="fas fa-pencil fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                                                </a>
                                                <form class="p-3" action="{{ route(get_class($transaction) === 'App\Models\Earning' ? 'earnings.delete' : 'spendings.delete', [ $transaction->id ]) }}" method="POST">
                                                    {{ method_field('DELETE') }}
                                                    {{ csrf_field() }}
                                                    <button class="button link">
                                                        <i class="fas fa-trash fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="transaction-block" id="transaction-chart" style="display: none;">
                        <canvas id="transactionsChartBar"></canvas>
                        <div id="transactionsChartCircleContainer">
                            <canvas id="transactionsChartCircle"></canvas>
                        </div>
                    </div>
                    <div class="transaction-block" id="transaction-tags" style="display: none;">
                        @include('partials.tags_price', ['tagsPrice' => $tagsPrice])
                    </div>
                @else
                    <div class="box">
                        @include('partials.empty_state', ['payload' => 'transactions'])
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('js/chartjs.js') }}"></script>
    <script>
        //TODO : change that on vueJs component
        function changeTab(evt, tabName) {
            // Declare all variables
            let i, tabcontent, tablinks;

            tabcontent = document.getElementsByClassName("transaction-block");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }

            tablinks = document.getElementsByClassName("transaction-link");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }

            document.getElementById("transaction-"+tabName).style.display = "block";
            evt.className += " active";
        }

        @if ($transactionsChart)
            let labels = [];
            let transactionsData = [];
            let transactionsColor = [];
            let testDataset = [];
            @foreach($transactionsChart as $label => $content)
                testDataset.push({
                    label: '{{ $label }}',
                    data: ['{{ $content['amount'] }}'],
                    backgroundColor: [ '{{ $content['color'] }}' ],
                })
                labels.push('{{ $label }}');
                transactionsData.push('{{ $content['amount'] }}');
                transactionsColor.push('{{ $content['color'] }}');
            @endforeach

            let label = '{{ __('models.transactions') }}';
            const data = {
                labels: labels,
                datasets: [{
                    label: label,
                    data: transactionsData,
                    backgroundColor: transactionsColor,
                    hoverOffset: 2
                }]
            };

            const barChart = new Chart(
                document.getElementById('transactionsChartBar'),
                {
                    type: 'bar',
                    data: data
                }
            );

            const circleChart = new Chart(
                document.getElementById('transactionsChartCircle'),
                {
                    type: 'pie',
                    data: data
                }
            )
        @endif

    </script>
@endsection
