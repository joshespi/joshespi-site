@php
    use App\Livewire\IntakeForm;
    $field = 'w-full border border-border rounded px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent';
@endphp

<div>
    @if($submitted)
        <div class="text-center py-8">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-2">Got it — I'll be in touch.</h3>
            <p class="text-muted text-sm">Expect a written response within 1 business day.</p>
        </div>
    @else
        <form wire:submit="submit" class="space-y-6">

            {{-- Name + Email --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="name">Your name</label>
                    <input wire:model="name" id="name" type="text" autocomplete="name"
                           class="{{ $field }} @error('name') border-red-400 @enderror"
                           placeholder="Jane Smith">
                    <x-form-error field="name" />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="email">Email address</label>
                    <input wire:model="email" id="email" type="email" autocomplete="email"
                           class="{{ $field }} @error('email') border-red-400 @enderror"
                           placeholder="jane@example.com">
                    <x-form-error field="email" />
                </div>
            </div>

            {{-- Service --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" for="service">Service needed</label>
                <select wire:model.live="service" id="service"
                        class="{{ $field }} @error('service') border-red-400 @enderror">
                    <option value="">Select a service...</option>
                    @foreach(IntakeForm::serviceMenu() as $group => $options)
                    <optgroup label="{{ $group }}">
                        @foreach($options as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                    <option value="Not sure / other">Not sure / other</option>
                </select>
                <x-form-error field="service" />
            </div>

            {{-- Site URL (shown once a service is selected) --}}
            @if($service)
            <div>
                <label class="block text-sm font-semibold mb-1.5" for="url">Site URL <span class="font-normal text-muted">(if applicable)</span></label>
                <input wire:model="url" id="url" type="text"
                       class="{{ $field }} @error('url') border-red-400 @enderror"
                       placeholder="https://example.com">
                <x-form-error field="url" />
            </div>
            @endif

            {{-- WordPress conditional fields --}}
            @if($this->isWpService())
            <x-intake-fields-box>
                <div>
                    <p class="block text-sm font-semibold mb-2">Do you have WordPress admin access?</p>
                    <div class="flex flex-wrap gap-4">
                        @foreach(['Yes', 'Not yet', 'Need help getting it', 'No'] as $opt)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input wire:model="wpAdminAccess" type="radio" value="{{ $opt }}" class="accent-brand">
                            {{ $opt }}
                        </label>
                        @endforeach
                    </div>
                    <x-form-error field="wpAdminAccess" />
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="hostingProvider">Hosting provider <span class="font-normal text-muted">(optional)</span></label>
                    <input wire:model="hostingProvider" id="hostingProvider" type="text"
                           class="{{ $field }}"
                           placeholder="WP Engine, SiteGround, Bluehost...">
                    <p class="text-xs text-muted mt-1">Not sure? Check your billing emails or domain registrar — or just leave it blank.</p>
                </div>

                @if($this->isMigration())
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="currentHost">Current host you're migrating from</label>
                    <input wire:model="currentHost" id="currentHost" type="text"
                           class="{{ $field }}"
                           placeholder="GoDaddy, Bluehost...">
                    <p class="text-xs text-muted mt-1">Check your billing emails if you're not sure — or just leave it blank.</p>
                </div>
                @endif
            </x-intake-fields-box>
            @endif

            {{-- Custom Dev conditional fields --}}
            @if($this->isCustomDevService())
            <x-intake-fields-box>
                <div>
                    <p class="block text-sm font-semibold mb-2">Starting point</p>
                    <div class="flex flex-wrap gap-4">
                        @foreach(['Existing code to extend', 'Starting from scratch'] as $opt)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input wire:model="startingPoint" type="radio" value="{{ $opt }}" class="accent-brand">
                            {{ $opt }}
                        </label>
                        @endforeach
                    </div>
                    <x-form-error field="startingPoint" />
                </div>

                @if($this->isApiIntegration())
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="integrations">Required integrations / APIs</label>
                    <input wire:model="integrations" id="integrations" type="text"
                           class="{{ $field }}"
                           placeholder="Stripe, Salesforce, QuickBooks...">
                    <p class="text-xs text-muted mt-1">If you're not sure of the exact name, describe what you need it to connect to — I can figure out the rest.</p>
                </div>
                @endif
            </x-intake-fields-box>
            @endif

            {{-- DevOps conditional fields --}}
            @if($this->isDevOpsService())
            <x-intake-fields-box>
                <label class="block text-sm font-semibold mb-1.5" for="stackLanguage">Stack / language</label>
                <input wire:model="stackLanguage" id="stackLanguage" type="text"
                       class="{{ $field }}"
                       placeholder="PHP/Laravel, Node, Python, Docker...">
                <p class="text-xs text-muted mt-1">Not sure? That's fine — just describe what the app does and I'll ask follow-up questions.</p>
            </x-intake-fields-box>
            @endif

            {{-- Budget + Timeline --}}
            @if($service)
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="budget">Budget range</label>
                    <select wire:model="budget" id="budget"
                            class="{{ $field }} @error('budget') border-red-400 @enderror">
                        <option value="">Select...</option>
                        <option value="Under $500">Under $500</option>
                        <option value="$500–$1,500">$500–$1,500</option>
                        <option value="$1,500–$5,000">$1,500–$5,000</option>
                        <option value="$5,000+">$5,000+</option>
                        <option value="Not sure">Not sure</option>
                    </select>
                    <x-form-error field="budget" />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="timeline">Timeline</label>
                    <select wire:model="timeline" id="timeline"
                            class="{{ $field }} @error('timeline') border-red-400 @enderror">
                        <option value="">Select...</option>
                        <option value="ASAP">ASAP</option>
                        <option value="Within 2 weeks">Within 2 weeks</option>
                        <option value="Within a month">Within a month</option>
                        <option value="Flexible">Flexible</option>
                    </select>
                    <x-form-error field="timeline" />
                </div>
            </div>
            @endif

            {{-- Message --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" for="message">Tell me about the project</label>
                <textarea wire:model="message" id="message" rows="5"
                          class="{{ $field }} resize-y @error('message') border-red-400 @enderror"
                          placeholder="What's broken, slow, or missing? What does success look like?"></textarea>
                <x-form-error field="message" />
            </div>

            <div class="flex items-center justify-between">
                <p class="text-xs text-muted">I'll respond by email within 1 business day.</p>
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="bg-brand hover:bg-brand-dark disabled:opacity-60 text-white font-semibold px-8 py-3 rounded transition-colors">
                    <span wire:loading.remove>Send &rarr;</span>
                    <span wire:loading>Sending...</span>
                </button>
            </div>

        </form>
    @endif
</div>
