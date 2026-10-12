@php
    use App\Livewire\IntakeForm;
@endphp

<div>
    @if($submitted)
        <div role="status" class="text-center py-8">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            {{-- Focused on arrival: the form a screen reader user was in has just
                 vanished, and this is where they should land instead. --}}
            <h3 tabindex="-1" x-data x-init="$el.focus()" class="text-xl font-bold mb-2 focus:outline-none">Got it — I'll be in touch.</h3>
            <p class="text-muted text-sm">Expect a written response within 1 business day.</p>
        </div>
    @else
        {{-- novalidate: the required attributes are for assistive tech; the
             server's messages are the ones people should see, not the
             browser's own popups. --}}
        <form wire:submit="submit" novalidate class="space-y-6"
              x-data
              x-on:intake-invalid.window="$nextTick(() => $el.querySelector('[aria-invalid=true]')?.focus())">

            {{-- Whole-form problems, e.g. rate limiting. These belong to no
                 single field, so they get their own banner. --}}
            @error('form')
            <div role="alert" class="border border-red-300 bg-red-50 text-red-700 rounded px-4 py-3 text-sm">
                {{ $message }}
            </div>
            @enderror

            {{-- Honeypot. Off-screen rather than display:none, which some bots
                 check for. Never autofilled: no autocomplete, not tabbable. --}}
            <div aria-hidden="true" class="absolute w-px h-px -left-[9999px] overflow-hidden">
                <label for="website">Leave this field empty</label>
                <input wire:model="website" id="website" name="website" type="text"
                       tabindex="-1" autocomplete="off">
            </div>

            <p class="text-xs text-muted">All fields are required unless marked optional.</p>

            {{-- Name + Email --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="name">Your name</label>
                    <input wire:model="name" id="name" type="text" autocomplete="name" required
                           @invalid('name')
                           class="field"
                           placeholder="Jane Smith">
                    <x-form-error field="name" />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="email">Email address</label>
                    <input wire:model="email" id="email" type="email" autocomplete="email" required
                           @invalid('email')
                           class="field"
                           placeholder="jane@example.com">
                    <x-form-error field="email" />
                </div>
            </div>

            {{-- Service --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" for="service">Service needed</label>
                <select wire:model.live="service" id="service" required
                        @invalid('service')
                        class="field">
                    <option value="">Select a service...</option>
                    @foreach(IntakeForm::serviceMenu() as $group => $options)
                    <optgroup label="{{ $group }}">
                        @foreach($options as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                    <option value="{{ IntakeForm::OTHER_SERVICE }}">{{ IntakeForm::OTHER_SERVICE }}</option>
                </select>
                <x-form-error field="service" />
            </div>

            {{-- Site URL (shown once a service is selected). Text rather than
                 type=url, which would reject "example.com" without a scheme. --}}
            @if($service)
            <div>
                <label class="block text-sm font-semibold mb-1.5" for="url">Site URL <span class="font-normal text-muted">(optional)</span></label>
                <input wire:model="url" id="url" type="text" inputmode="url" autocomplete="url"
                       @invalid('url')
                       class="field"
                       placeholder="https://example.com">
                <x-form-error field="url" />
            </div>
            @endif

            {{-- WordPress conditional fields --}}
            @if($this->isWpService())
            <x-intake-fields-box>
                <fieldset>
                    <legend class="block text-sm font-semibold mb-2">Do you have WordPress admin access?</legend>
                    <div class="flex flex-wrap gap-4">
                        @foreach(IntakeForm::WP_ADMIN_ACCESS as $opt)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input wire:model="wpAdminAccess" type="radio" name="wpAdminAccess" value="{{ $opt }}" required
                                   @invalid('wpAdminAccess')
                                   class="accent-brand">
                            {{ $opt }}
                        </label>
                        @endforeach
                    </div>
                    <x-form-error field="wpAdminAccess" />
                </fieldset>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="hostingProvider">Hosting provider <span class="font-normal text-muted">(optional)</span></label>
                    <input wire:model="hostingProvider" id="hostingProvider" type="text"
                           aria-describedby="hostingProvider-hint"
                           class="field"
                           placeholder="WP Engine, SiteGround, Bluehost...">
                    <p id="hostingProvider-hint" class="text-xs text-muted mt-1">Not sure? Check your billing emails or domain registrar — or just leave it blank.</p>
                </div>

                @if($this->isMigration())
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="currentHost">Current host you're migrating from <span class="font-normal text-muted">(optional)</span></label>
                    <input wire:model="currentHost" id="currentHost" type="text"
                           aria-describedby="currentHost-hint"
                           class="field"
                           placeholder="GoDaddy, Bluehost...">
                    <p id="currentHost-hint" class="text-xs text-muted mt-1">Check your billing emails if you're not sure — or just leave it blank.</p>
                </div>
                @endif
            </x-intake-fields-box>
            @endif

            {{-- Custom Dev conditional fields --}}
            @if($this->isCustomDevService())
            <x-intake-fields-box>
                <fieldset>
                    <legend class="block text-sm font-semibold mb-2">Starting point</legend>
                    <div class="flex flex-wrap gap-4">
                        @foreach(IntakeForm::STARTING_POINTS as $opt)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input wire:model="startingPoint" type="radio" name="startingPoint" value="{{ $opt }}" required
                                   @invalid('startingPoint')
                                   class="accent-brand">
                            {{ $opt }}
                        </label>
                        @endforeach
                    </div>
                    <x-form-error field="startingPoint" />
                </fieldset>

                @if($this->isApiIntegration())
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="integrations">Required integrations / APIs <span class="font-normal text-muted">(optional)</span></label>
                    <input wire:model="integrations" id="integrations" type="text"
                           aria-describedby="integrations-hint"
                           class="field"
                           placeholder="Stripe, Salesforce, QuickBooks...">
                    <p id="integrations-hint" class="text-xs text-muted mt-1">If you're not sure of the exact name, describe what you need it to connect to — I can figure out the rest.</p>
                </div>
                @endif
            </x-intake-fields-box>
            @endif

            {{-- DevOps conditional fields --}}
            @if($this->isDevOpsService())
            <x-intake-fields-box>
                <label class="block text-sm font-semibold mb-1.5" for="stackLanguage">Stack / language <span class="font-normal text-muted">(optional)</span></label>
                <input wire:model="stackLanguage" id="stackLanguage" type="text"
                       aria-describedby="stackLanguage-hint"
                       class="field"
                       placeholder="PHP/Laravel, Node, Python, Docker...">
                <p id="stackLanguage-hint" class="text-xs text-muted mt-1">Not sure? That's fine — just describe what the app does and I'll ask follow-up questions.</p>
            </x-intake-fields-box>
            @endif

            {{-- Budget + Timeline. Options come from the component so the
                 validator's allowlist and this markup cannot disagree. --}}
            @if($service)
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="budget">Budget range</label>
                    <select wire:model="budget" id="budget" required
                            @invalid('budget')
                            class="field">
                        <option value="">Select...</option>
                        @foreach(IntakeForm::BUDGETS as $opt)
                        <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    <x-form-error field="budget" />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" for="timeline">Timeline</label>
                    <select wire:model="timeline" id="timeline" required
                            @invalid('timeline')
                            class="field">
                        <option value="">Select...</option>
                        @foreach(IntakeForm::TIMELINES as $opt)
                        <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    <x-form-error field="timeline" />
                </div>
            </div>
            @endif

            {{-- Message --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" for="message">Tell me about the project</label>
                <textarea wire:model="message" id="message" rows="5" required
                          @invalid('message')
                          class="field resize-y"
                          placeholder="What's broken, slow, or missing? What does success look like?"></textarea>
                <x-form-error field="message" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <p class="text-xs text-muted">I'll respond by email within 1 business day.</p>
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="bg-brand hover:bg-brand-dark disabled:opacity-60 text-white font-semibold px-8 py-3 rounded transition-colors shrink-0">
                    <span wire:loading.remove>Send &rarr;</span>
                    <span wire:loading>Sending...</span>
                </button>
            </div>

        </form>
    @endif
</div>
