<?php

namespace App\Http\Livewire\Tasks;

use App\Models\Task;
use Closure;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $listeners = ['update'];

    public string $todo;
    public $confirmingDeleteTask = false;
    public Task $taskToDelete;

    public array $tasksGlobal;

    protected $rules = [
        'todo' => 'required|string|max:200',
    ];

    public function render(): View|Closure|string
    {
        $tasks = Task::orderByDesc('id')->paginate(10);
        $alertDeleteTask = $this->confirmingDeleteTask;

        $this->tasksGlobal = Task::orderByDesc('id')->paginate(10)->toArray();

        return view('livewire.tasks.index', compact('tasks', 'alertDeleteTask'));
    }

    public function submit(): void
    {
        $this->validate();

        Task::create([
            'todo' => $this->todo,
        ]);

        $this->todo = '';
        $this->emit('created');
    }

    public function completed(Task $task): void
    {
        if ($task->done) {
            $task->update([
                'done' => 0,
            ]);
        } else {
            $task->update([
                'done' => 1,
            ]);
        }

        $this->emit('completed');
    }

    public function update(int $taskId, string $updateTodo): void
    {
        if ($updateTodo == '') return;

        Task::where('id', $taskId)->update([
            'todo' => $updateTodo,
        ]);

        $this->emit('updated');
    }

    public function selectedTaskToDelete(Task $task): void
    {
        $this->confirmingDeleteTask = true;
        $this->taskToDelete = $task;
    }

    public function delete():void
    {
        $this->confirmingDeleteTask = false;
        $this->taskToDelete->delete();
        $this->emit('deleted');
    }

}
