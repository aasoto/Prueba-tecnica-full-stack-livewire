<?php

namespace App\Http\Livewire\Tasks;

use App\Models\Task;
use Closure;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $listeners = ['update'];

    protected $queryString = ['search', 'doneStatus'];

    public string $todo;
    public bool $confirmingDeleteTask = false;
    public Task $taskToDelete;

    public string $search = '', $doneStatus = '';

    protected $rules = [
        'todo' => 'required|string|max:200',
    ];

    public function render(): View|Closure|string
    {
        $tasks = Task::orderByDesc('id');

        if ($this->search) {
            $tasks->where('todo', 'like', '%'.$this->search.'%');
        }

        switch ($this->doneStatus) {
            case 'completed':
                $tasks->where('done', '1');
                break;
            case 'uncompleted':
                $tasks->where('done', '0');
                break;
        }

        $tasks = $tasks->paginate(10);
        $alertDeleteTask = $this->confirmingDeleteTask;

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
            $this->emit('uncompleted');
        } else {
            $task->update([
                'done' => 1,
            ]);
            $this->emit('completed');
        }

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
