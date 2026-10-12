@php
    use App\Enums\IntakeStatus;
    use App\Livewire\Admin\IntakeIndex;

    $chip = 'px-3 py-1.5 rounded-full text-sm font-medium border transition-colors';
    $chipOn = 'bg-ink text-white border-ink';
    $chipOff = 'bg-white text-ink border-border hover:border-ink';
@endphp

<div>
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
        <h1 class="text-3xl font-black">Intakes</h1>

        <div class="md:w-72">
            <label for="search" class="sr-only">Search intakes</label>
            <input wire:model.live.debounce.300ms="search" id="search" type="search"
                   placeholder="Search name, email, service..."
                   class="field">
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mb-6" role="group" aria-label="Filter by status">
        <button type="button" wire:click="$set('filter', '')" aria-pressed="{{ $filter === '' ? 'true' : 'false' }}"
                class="{{ $chip }} {{ $filter === '' ? $chipOn : $chipOff }}">
            All <span class="opacity-60">{{ $total }}</span>
        </button>

        @foreach(IntakeStatus::cases() as $status)
            <button type="button" wire:click="$set('filter', '{{ $status->value }}')"
                    aria-pressed="{{ $filter === $status->value ? 'true' : 'false' }}"
                    class="{{ $chip }} {{ $filter === $status->value ? $chipOn : $chipOff }}">
                {{ $status->label() }} <span class="opacity-60">{{ $counts[$status->value] ?? 0 }}</span>
            </button>
        @endforeach

        @if($undelivered > 0 || $filter === IntakeIndex::UNDELIVERED)
            <button type="button" wire:click="$set('filter', '{{ IntakeIndex::UNDELIVERED }}')"
                    aria-pressed="{{ $filter === IntakeIndex::UNDELIVERED ? 'true' : 'false' }}"
                    class="{{ $chip }} {{ $filter === IntakeIndex::UNDELIVERED ? 'bg-red-700 text-white border-red-700' : 'bg-red-50 text-red-700 border-red-300 hover:border-red-700' }}">
                Email failed <span class="opacity-70">{{ $undelivered }}</span>
            </button>
        @endif
    </div>

    @if($intakes->isEmpty())
        <div class="bg-white border border-border rounded-lg p-10 text-center text-muted">
            {{ $search !== '' || $filter !== '' ? 'No intakes match.' : 'No intakes yet.' }}
        </div>
    @else
        <div class="bg-white border border-border rounded-lg overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-muted border-b border-border">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-semibold">#</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Received</th>
                        <th scope="col" class="px-4 py-3 font-semibold">From</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Service</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Budget</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($intakes as $intake)
                        <tr wire:key="intake-{{ $intake->id }}" class="border-b border-border last:border-0 hover:bg-canvas">
                            <td class="px-4 py-3 text-muted font-mono">{{ $intake->id }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <time datetime="{{ $intake->created_at->toIso8601String() }}" title="{{ $intake->created_at->format('M j, Y g:ia') }}">
                                    {{ $intake->created_at->diffForHumans() }}
                                </time>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.intakes.show', $intake) }}" class="font-semibold hover:text-brand">
                                    {{ $intake->name }}
                                </a>
                                <div class="text-muted">{{ $intake->email }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $intake->service }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $intake->budget }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $intake->status->badgeClasses() }}">
                                    {{ $intake->status->label() }}
                                </span>
                                @if($intake->isUndelivered())
                                    <span class="ml-1 text-xs font-semibold text-red-700">email failed</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $intakes->links() }}
        </div>
    @endif
</div>
