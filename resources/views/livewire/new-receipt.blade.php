<div>
    <form wire:submit="store">
        <div class="grid gap-4 grid-cols-[1fr,2fr]">
            <x-card class="p-2">
                @if($image)
                    <div class="aspect-[1/2]" wire:loading.class="animate-scan" wire:target="autofill">
                        <img class="rounded shadow w-full h-full object-cover" src="{{ $image->temporaryUrl() }}" alt="">
                    </div>
                @else
                    <div x-data="{ active: false }" :class="active && 'border-accent'" class="aspect-[1/2] rounded border border-dashed p-2 hover:border-accent transition-colors" wire:loading.class="animate-scan" wire:target="autofill">
                        <div class="relative h-full cursor-pointer text-gray-400 flex gap-2 flex-col items-center justify-center" role="button">
                            <input
                                class="w-full h-full absolute top-0 left-0 cursor-pointer opacity-0"
                                type="file"
                                wire:model.live="image"
                                accept=".png,.jpg,.jpeg"
                                x-on:dragover="active = true"
                                x-on:dragleave="active = false"
                                x-on:drop="active = false"
                            />

                            <x-icon name="upload" class="w-4 h-4"/>
                            <p class="text-sm">Click to add photo</p>
                        </div>
                    </div>
                @endif
            </x-card>

            <x-card class="p-2 flex flex-col gap-2">
                <x-input required wire:model="form.store" placeholder="Store"/>
                <x-input required wire:model="form.amount" type="text" inputmode="tel" placeholder="Amount" prefix="$"/>

                <x-select required wire:model="form.category_id" placeholder="Category...">
                    @foreach($categories->where('qualified') as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach

                    <hr>

                    @foreach($categories->where('qualified', false) as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-select>

                <x-select required wire:model="form.envelope_id" placeholder="Envelope...">
                    @foreach($envelopes as $envelope)
                        <option value="{{ $envelope->id }}">{{ $envelope->name }}</option>
                    @endforeach
                </x-select>

                <x-textarea wire:model="form.description" placeholder="Description"/>

                <div class="flex gap-2">
                    @if($image)
                        <x-button type="button" wire:click="$set('image', '')" icon="trash" color="red"/>

                        @can('use-ai')
                            <div class="flex gap-2 items-center">
                                <x-button
                                    wire:click="autofill"
                                    text="Scan"
                                    type="button"
                                    icon="sparkles"
                                    color="purple"
                                    wire:target="autofill"
                                    :disabled="$autofillSuccess !== null"
                                />

                                @if($autofillCents && $autofillSeconds)
                                    <div class="leading-[1.1] text-[0.6rem] text-gray-400">
                                        <p>{{ number_format($autofillCents, 1) }}¢</p>
                                        <p>{{ number_format($autofillSeconds, 1) }}s</p>
                                    </div>
                                @endif
                            </div>
                        @endcan
                    @endif

                    <div class="ml-auto">
                        <x-button text="Add"/>
                    </div>
                </div>
            </x-card>
        </div>
    </form>
</div>
