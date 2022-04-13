@extends('emails.template')

@section('content')
    {{ trans('email.here_weekly_report', ['space' => $space->name]) }}

    <p>
        {{ trans('email.this_week_you_have_spent', ['week' => $week]) }} {!! $space->currency->symbol !!} {{ \App\Helper::formatNumber($totalSpent / 100) }}
    </p>

    @if (count($largestSpendingWithTag))
        <table>
            <thead>
                <tr>
                    <th>{{ trans('models.tag') }}</th>
                    <th>{{ trans('general.spent') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($largestSpendingWithTag as $spendingTag)
                    <tr>
                        <td>{{ $spendingTag->tag_name }}</td>
                        <td>{!! $space->currency->symbol !!} {{ \App\Helper::formatNumber($spendingTag->amount / 100) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{ trans('reports.tired_report') }} <a href="{{ config('app.url') . '/settings/preferences' }}">{{ trans('general.change_your_preference') }}</a>.
@endsection
