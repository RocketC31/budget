@if (count($tagsPrice))
    <div class="box mt-3">
        @foreach ($tagsPrice as $index => $tag)
            <div class="box__section row row--seperate">
                <div class="row__column row__column--middle color-dark">
                    @include('partials.tag', ['payload' => $tag])
                </div>
                @isset($totalSpent)
                    <div class="row__column row__column--middle">
                        <progress max="{{ $totalSpent }}" value="{{ $tag->amount }}"></progress>
                    </div>
                    <div class="row__column row__column--middle text-right">{!! $currency !!} {{ \App\Helper::formatNumber($tag->amount / 100) }} / {!! $currency !!} {{ \App\Helper::formatNumber($totalSpent / 100) }}</div>
                @else
                    <div class="row__column row__column--middle text-right">{!! $currency !!} {{ \App\Helper::formatNumber($tag->amount / 100) }}</div>
                @endisset
            </div>
        @endforeach
    </div>
@endif
