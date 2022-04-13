{{ trans('email.here_weekly_report', ['space' => $space->name]) }}

{{ trans('email.this_week_you_have_spent', ['week' => $week]) }} {!! $space->currency->symbol !!} {{ \App\Helper::formatNumber($totalSpent / 100) }}

@if (count($largestSpendingWithTag))
    @foreach($largestSpendingWithTag as $spendingTag)
        {{ $spendingTag->tag_name }} : {!! $space->currency->symbol !!} {{ \App\Helper::formatNumber($spendingTag->amount / 100) }}
    @endforeach
@endif

{{ trans('reports.tired_report') }}

{{ config('app.url') . '/settings/preferences' }}
