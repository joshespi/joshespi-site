<?php

namespace App\Livewire\Admin;

use App\Enums\IntakeEmail;
use App\Enums\IntakeStatus;
use App\Models\Intake;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts::admin')]
class IntakeShow extends Component
{
    #[Locked]
    public Intake $intake;

    public string $status = '';

    public string $note = '';

    public function mount(Intake $intake): void
    {
        $this->intake = $intake;
        $this->status = $intake->status->value;
    }

    public function updatedStatus(): void
    {
        $this->validate(['status' => ['required', Rule::enum(IntakeStatus::class)]]);

        $this->intake->update(['status' => $this->status]);
    }

    public function addNote(): void
    {
        $this->validate(['note' => 'required|string|max:5000']);

        $this->intake->notes()->create(['body' => $this->note]);
        $this->reset('note');
    }

    public function deleteNote(int $id): void
    {
        // Scoped through the relation, so only this intake's notes are reachable.
        $this->intake->notes()->whereKey($id)->delete();
    }

    public function resend(string $email): void
    {
        if ($email = IntakeEmail::tryFrom($email)) {
            $this->intake->deliver($email);
        }
    }

    public function delete()
    {
        $this->intake->delete();

        return $this->redirectRoute('admin.intakes.index');
    }

    public function render()
    {
        return view('livewire.admin.intake-show', [
            'notes' => $this->intake->notes()->get(),
        ])->title("#{$this->intake->id} {$this->intake->name}");
    }
}
