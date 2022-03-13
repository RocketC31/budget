@extends('layout')

@section('title', __('models.transactions'))

@section('body')
    <div class="wrapper my-3">
        <div class="row mb-3">
            <div class="row__column row__column--middle">
                <h2>{{ __('models.transactions') }}</h2>
            </div>
            <div class="row__column row__column--compact row__column--middle">
                <a href="{{ route('transactions.create') }}" class="button">{{ __('actions.create') }} {{ __('models.transactions') }}</a>
            </div>
        </div>
        <div class="row row--responsive">
            <div class="row__column mr-3" style="max-width: 300px;">
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
            <div class="row__column">
                <h2 class="mb-2">
                    <a href="{{ route('transactions.index', ['monthIndex' => ($currentMonthIndex - 1) ]) }}"><i class="fa fa-chevron-left"></i></a>
                    {{ __('calendar.months.' . $month) }}, {{ $year }}
                    <a href="{{ route('transactions.index', ['monthIndex' => ($currentMonthIndex + 1) ]) }}"><i class="fa fa-chevron-right"></i></a>
                </h2>
                @if ($transactions)
                    <div class="box">
                        @foreach ($transactions as $key => $transaction)
                            <div class="box__section row row--responsive">
                                <div class="row__column row__column--middle row row--middle">
                                    <div v-pre>
                                        {{ $transaction->description }}
                                        <br> <span class="date">{{ $transaction->happened_on->format('d') }} {{ __('calendar.months.' . $month) }}, {{ $year }}</span>
                                    </div>
                                    <a href="{{ route(get_class($transaction) === 'App\Models\Earning' ? 'earnings.show' : 'spendings.show', [ $transaction->id ]) }}">
                                        <i class="fas fa-info-circle fa-xs c-light ml-1"></i>
                                    </a>
                                    <a href="{{ route(get_class($transaction) === 'App\Models\Earning' ? 'earnings.edit' : 'spendings.edit', [ $transaction->id ]) }}">
                                        <i class="fas fa-pencil fa-xs c-light ml-1"></i>
                                    </a>
                                    <form action="{{ route(get_class($transaction) === 'App\Models\Earning' ? 'earnings.delete' : 'spendings.delete', [ $transaction->id ]) }}" method="POST">
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
                                        <button class="button link">
                                            <i class="fas fa-trash fa-xs c-light ml-1"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="row__column">
                                    @if ($transaction->tag)
                                        <div class="row">
                                            <div class="row__column row__column--compact row__column--middle mr-05" style="font-size: 12px;">
                                                <i class="fas fa-tag" style="color: #{{ $transaction->tag->color }};"></i>
                                            </div>
                                            <div class="row__column row__column--compact row__column--middle" v-pre>{{ $transaction->tag->name }}</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row__column row__column--compact w-50">
                                    @if ($transaction->recurring_id)
                                        <i class="fas fa-recycle"></i>
                                    @endif
                                </div>
                                <div class="row__column row__column--compact row__column--middle w-150 row row--middle row--right {{ get_class($transaction) == 'App\Models\Earning' ? 'color-green' : 'color-red' }}">
                                    <div class="row__column row__column--compact">{!! $currency !!} {{ $transaction->formatted_amount }}</div>
                                    <div class="row__column row__column--compact ml-1">
                                        @if (get_class($transaction) == 'App\Models\Earning')
                                            <i class="fas fa-arrow-alt-left fa-sm"></i>
                                        @else
                                            <i class="fas fa-arrow-alt-right fa-sm"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
