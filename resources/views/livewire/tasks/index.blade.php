<div x-data="data()" class="bg-white rounded-md mx-5 px-8 py-5 shadow-lg flex flex-col justify-center items-center gap-5">
    <x-small-alert on="created" class="bg-green-200 rounded-md w-max px-5 py-2">
        Agregada exitosamente
    </x-small-alert>
    <x-small-alert on="completed" class="bg-green-200 rounded-md w-max px-5 py-2">
        Completada exitosamente
    </x-small-alert>
    <x-small-alert on="updated" class="bg-green-200 rounded-md w-max px-5 py-2">
        Tarea editada exitosamente
    </x-small-alert>

    @if ($alertDeleteTask)
        <x-modal>
            <x-slot name="title">
                <div class="">
                    {{ __('Eliminar tarea') }}
                </div>
            </x-slot>

            <x-slot name="content">
                {{ __('¿Está seguro de que desea borrar esta tarea?') }}
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingDeleteTask')" wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ml-3" wire:click="delete()" wire:loading.attr="disabled">
                    {{ __('Eliminar') }}
                </x-danger-button>
            </x-slot>
        </x-modal>
    @endif

    <form wire:submit.prevent="submit" class="w-full">
        <x-error-message for="todo"/>
        <div class="grid grid-cols-4 gap-4 w-full">
            <div class="col-span-3">
                <input
                    wire:model="todo"
                    type="text"
                    class="border border-gray-500 rounded-md px-5 py-2 placeholder:italic w-full"
                    placeholder="Nueva tarea"
                />
            </div>
            <div class="col-span-1">
                <button
                    type="submit"
                    class="bg-green-600 text-white font-bold h-full w-full px-4 py-2 rounded-md hover:bg-green-700 scale-100 hover:scale-105 transition duration-200"
                >
                    Guardar
                </button>
            </div>
        </div>
    </form>
    <table class="w-full">
        <thead>
            <tr>
                <th class="bg-gray-300 rounded-tl-2xl font-bold px-2 py-5">
                    ID
                </th>
                <th class="bg-gray-300 font-bold px-2 py-5">
                    Hacer
                </th>
                <th class="bg-gray-300 font-bold px-2 py-5">
                    Completado
                </th>
                <th class="bg-gray-300 rounded-tr-2xl font-bold px-2 py-5">
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                <tr class="border-b border-gray-400">
                    <td class="font-bold pl-4 py-2 text-center">
                        {{ $task->id }}
                    </td>
                    <td class="capitalize pl-4 py-2">
                        <span
                            :class="(editing == true) && (item == {{ $task->id }}) ? 'hidden': 'block'"
                            @click="edit({{$task->id}}, '{{$task->todo}}')"
                        >
                            {{ $task->todo }}
                        </span>
                        <template x-if="editing && item == {{ $task->id }}">
                            <input
                                :value="todo"
                                type="text"
                                class="border border-gray-500 rounded-md px-5 py-2 placeholder:italic w-full"
                                placeholder="Editar tarea"
                                @keyup.enter="update($event.target.value)"
                            />
                        </template>
                    </td>
                    <td class="capitalize pl-4 py-2 text-center">
                        @if ($task->done)
                            <input
                                checked
                                type="checkbox"
                                value=""
                                wire:click="completed({{$task}})"
                                class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            >
                        @else
                            <input
                                type="checkbox"
                                value=""
                                wire:click="completed({{$task}})"
                                class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            >
                        @endif
                    </td>
                    <td class="capitalize pl-4 py-2 flex justify-center items-center">
                        <button wire:click="selectedTaskToDelete({{ $task }})" class="bg-red-500 hover:bg-red-600 text-white font-bold p-2 rounded-md shadow-sm hover:shadow scale-100 hover:scale-105 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $tasks->links() }}
</div>
<script>
    const data = () => {
        return {
            editing: false,
            item: 0,
            todo: '',
            edit(item, todo){
                this.editing = !this.editing;
                this.item = item;
                this.todo = todo;
            },
            update(todo){
                Livewire.emit('update', this.item, todo);
                this.editing = false;
                this.item = 0;
            }
        };
    }
</script>
