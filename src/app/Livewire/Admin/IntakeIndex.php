<?php

namespace App\Livewire\Admin;

use App\Enums\IntakeStatus;
use App\Models\Intake;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Intakes')]
class IntakeIndex extends Component
{
    use WithPagination;

    /** Filter value for intakes with an email that never went out. */
    public const UNDELIVERED = 'undelivered';

    /** A status value, UNDELIVERED, or '' for everything. */
    #[Url(except: '')]
    public string $filter = '';

    #[Url(except: '')]
    public string $search = '';

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $term = trim($this->search);

        $intakes = Intake::query()
            ->when($this->filter === self::UNDELIVERED, fn (Builder $q) => $q->undelivered())
            ->when(IntakeStatus::tryFrom($this->filter), fn (Builder $q, IntakeStatus $status) => $q->where('status', $status))
            ->when($term !== '', fn (Builder $q) => $q->where(fn (Builder $q) => $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('service', 'like', "%{$term}%")))
            ->latest('id')
            ->paginate(25);

        $counts = Intake::query()->toBase()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('livewire.admin.intake-index', [
            'intakes'     => $intakes,
            'counts'      => $counts,
            'total'       => $counts->sum(),
            'undelivered' => Intake::undelivered()->count(),
        ]);
    }
}
