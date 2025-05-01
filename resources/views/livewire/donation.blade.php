<div>
    <div>

        <div class="grid grid-cols-3 gap-2 mb-8">
            @foreach($this->intervalOptions as $title => $value)
                <div>
                    <label class="relative cursor-pointer">
                        <input type="radio" wire:model.live="interval" value="{{$value}}" class="sr-only peer" />
                        <div class="text-center py-2 border border-white peer-checked:border-indigo-400 peer-checked:bg-indigo-600 peer-checked:text-white transition-colors">
                            {{$title}}
                        </div>
                    </label>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-4 gap-2 mt-4 mb-12">
            @foreach($this->amountOptions as $value)
                <label class="relative cursor-pointer">
                    <input type="radio" wire:model.live="amount" value="{{ $value }}" class="sr-only peer" />
                    <div class="text-center py-2 border border-white peer-checked:border-indigo-400 peer-checked:bg-indigo-600 peer-checked:text-white transition-colors">
                        {{ $value > 0 ? '$' . $value : 'Custom' }}
                    </div>
                </label>
            @endforeach
        </div>


        <div class="flex justify-between">
            <div class="w-1/3 pr-6">
                @if($amount === 0)
                    <input type="number" wire:model.live="customAmount" class="text-black border-none rounded-lg w-full text-2xl px-4 py-2"/>
                @endif
            </div>
            <div class="w-2/3">
                <button class="w-full bg-indigo-600 text-white font-semibold h-12 px-6 py-2 rounded-lg block hover:bg-indigo-700 transition-colors" wire:click="donate">{{$this->buttonTitle}}</button>
            </div>
        </div>
    </div>
</div>
