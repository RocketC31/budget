<div class="row row--separate row--bottom mt-3 mb-1">
    <h3>{{ __('models.attachments') }}</h3>
    <form method="POST" action="/attachments" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="transaction_type" value="{{ get_class($payload) === 'App\Models\Earning' ? 'earning' : 'spending' }}" />
        <input type="hidden" name="transaction_id" value="{{ $payload->id }}" />
        <input type="file" name="file" onchange="this.form.submit()" />
    </form>
</div>
<div class="box">
    @if (!$payload->attachments->count())
        <div class="box__section text-center">{{ __('general.empty_attachments', ['resource' => strtolower(__('models.transaction'))]) }} </div>
    @endif
    @foreach ($payload->attachments as $attachment)
        <div class="box__section row">
            <div>
                @if ($attachment->file_type !== 'pdf')
                    <img src="{{ $attachment->file_b64 }}" style="max-width: 100%; max-height: 200px; border-radius: 5px; vertical-align: top;" />
                @else
                    <div class="mb-1" style="display: flex; align-items: center; justify-content: center; width: 200px; height: 200px; border-radius: 5px; background: #EEE;">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <a href="/attachments/{{ $attachment->id }}/download">{{ __('actions.download') }}</a>
                @endif
            </div>
            <div class="ml-2">
                <form method="POST" action="/attachments/{{ $attachment->id }}/delete">
                    {{ csrf_field() }}
                    <button class="button link">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>
