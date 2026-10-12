@php
    use App\Enums\IntakeEmail;
    use App\Enums\IntakeStatus;

    $card = 'bg-white border border-border rounded-lg p-6';
    $replySubject = rawurlencode("Re: your intake — {$intake->service}");
@endphp

<div>
    <a href="{{ route('admin.intakes.index') }}" class="text-sm text-muted hover:text-ink">&larr; All intakes</a>

    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mt-4 mb-8">
        <div>
            <h1 class="text-3xl font-black break-words">{{ $intake->name }}</h1>
            <p class="text-muted text-sm mt-1">
                <span class="font-mono">#{{ $intake->id }}</span> &middot;
                received {{ $intake->created_at->format('M j, Y \a\t g:ia') }}
                ({{ $intake->created_at->diffForHumans() }})
            </p>
        </div>

        <div class="flex items-end gap-3 shrink-0">
            <div>
                <label for="status" class="block text-xs font-semibold text-muted mb-1">
                    Status <span wire:loading wire:target="status" class="font-normal">— saving…</span>
                </label>
                <select wire:model.live="status" id="status" class="field md:w-44">
                    @foreach(IntakeStatus::cases() as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </select>
            </div>
            <a href="mailto:{{ $intake->email }}?subject={{ $replySubject }}"
               class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2.5 rounded transition-colors whitespace-nowrap">
                Reply by email
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 space-y-6">
            <section class="{{ $card }}" aria-labelledby="message-heading">
                <h2 id="message-heading" class="font-bold mb-3">Message</h2>
                <p class="whitespace-pre-wrap break-words leading-relaxed">{{ $intake->message }}</p>
            </section>

            <section class="{{ $card }}" aria-labelledby="details-heading">
                <h2 id="details-heading" class="font-bold mb-3">Details</h2>
                <dl class="grid sm:grid-cols-[11rem_1fr] gap-x-6 gap-y-2 text-sm">
                    <dt class="text-muted">Email</dt>
                    <dd class="break-all"><a href="mailto:{{ $intake->email }}" class="text-brand hover:underline">{{ $intake->email }}</a></dd>

                    <dt class="text-muted">Site URL</dt>
                    <dd class="break-all">
                        @if($intake->safeUrl())
                            <a href="{{ $intake->safeUrl() }}" target="_blank" rel="noopener noreferrer nofollow" class="text-brand hover:underline">{{ $intake->url }}</a>
                        @else
                            {{ $intake->url ?? '—' }}
                        @endif
                    </dd>

                    <dt class="text-muted">Service</dt>
                    <dd>{{ $intake->service }}</dd>

                    <dt class="text-muted">Budget</dt>
                    <dd>{{ $intake->budget }}</dd>

                    <dt class="text-muted">Timeline</dt>
                    <dd>{{ $intake->timeline }}</dd>

                    @foreach($intake->details ?? [] as $label => $value)
                        <dt class="text-muted">{{ $label }}</dt>
                        <dd class="break-words">{{ $value }}</dd>
                    @endforeach
                </dl>
            </section>
        </div>

        <div class="space-y-6">
            <section class="{{ $card }}" aria-labelledby="notes-heading">
                <h2 id="notes-heading" class="font-bold mb-3">Notes</h2>

                <form wire:submit="addNote" class="space-y-2 mb-5">
                    <label for="note" class="sr-only">New note</label>
                    <textarea wire:model="note" id="note" rows="3"
                              @invalid('note')
                              class="field resize-y" placeholder="Quoted $349, waiting on hosting login..."></textarea>
                    <x-form-error field="note" />
                    <button type="submit" wire:loading.attr="disabled" wire:target="addNote"
                            class="bg-ink hover:bg-black disabled:opacity-60 text-white text-sm font-semibold px-4 py-2 rounded transition-colors">
                        Add note
                    </button>
                </form>

                @forelse($notes as $note)
                    <article wire:key="note-{{ $note->id }}" class="border-t border-border pt-3 mt-3 first-of-type:border-0 first-of-type:pt-0">
                        <p class="text-sm whitespace-pre-wrap break-words">{{ $note->body }}</p>
                        <div class="flex items-center justify-between mt-1.5 text-xs text-muted">
                            <time datetime="{{ $note->created_at->toIso8601String() }}">{{ $note->created_at->format('M j, Y g:ia') }}</time>
                            <button type="button" wire:click="deleteNote({{ $note->id }})" wire:confirm="Delete this note?"
                                    class="hover:text-red-700">Delete</button>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-muted">No notes yet.</p>
                @endforelse
            </section>

            <section class="{{ $card }}" aria-labelledby="emails-heading">
                <h2 id="emails-heading" class="font-bold mb-3">Emails</h2>
                <ul class="space-y-4 text-sm">
                    @foreach(IntakeEmail::cases() as $email)
                        @php
                            $sentAt = $intake->sentAt($email);
                            $action = "resend('{$email->value}')";
                        @endphp
                        <li>
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-semibold">{{ $email->label() }}</span>
                                <button type="button"
                                        wire:click="{{ $action }}"
                                        @if($sentAt) wire:confirm="This already went out. Send it again?" @endif
                                        wire:loading.attr="disabled" wire:target="{{ $action }}"
                                        class="text-brand hover:underline disabled:opacity-60">
                                    <span wire:loading.remove wire:target="{{ $action }}">{{ $sentAt ? 'Resend' : 'Send now' }}</span>
                                    <span wire:loading wire:target="{{ $action }}">Sending…</span>
                                </button>
                            </div>
                            @if($sentAt)
                                <p class="text-green-800">Sent {{ $sentAt->format('M j, Y g:ia') }}</p>
                            @elseif($intake->emailError($email))
                                <p class="text-red-700 font-semibold">Failed</p>
                                <p class="text-xs text-muted break-words mt-0.5">{{ $intake->emailError($email) }}</p>
                            @else
                                <p class="text-muted">Not sent</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>

            <button type="button" wire:click="delete"
                    wire:confirm="Delete this intake and all its notes? This can't be undone."
                    class="text-sm text-muted hover:text-red-700">
                Delete intake
            </button>
        </div>
    </div>
</div>
