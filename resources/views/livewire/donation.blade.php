<div>
    <div class="donate-label">I would like to contribute:</div>

    <div class="donate-intervals">
        @foreach($this->intervalOptions as $title => $value)
            <label>
                <input type="radio" wire:model.live="interval" value="{{$value}}" style="display:none;" />
                <div class="donate-interval {{ $interval === $value ? 'active' : '' }}">
                    {{$title}}
                </div>
            </label>
        @endforeach
    </div>

    <div class="donate-amounts">
        @foreach($this->amountOptions as $value)
            <label>
                <input type="radio" wire:model.live="amount" value="{{ $value }}" style="display:none;" />
                <div class="donate-amount {{ $amount === $value ? 'active' : '' }}">
                    {{ $value > 0 ? '$' . $value : '$OTHER' }}
                </div>
            </label>
        @endforeach
    </div>

    @if($amount === 0)
        <input type="number" wire:model.live="customAmount" class="donate-custom-input" placeholder="Enter amount" min="1"/>
    @endif

    <button class="donate-submit" wire:click="donate" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed" style="position:relative;">
        <span wire:loading.remove>{{$this->buttonTitle}}</span>
        <span wire:loading>Processing...</span>
    </button>
</div>
