<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Importar Transações') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">


                <div class="mx-5 my-5 text-gray-900 dark:text-gray-100 space-y-3">

                    {{-- Chama compomente para exibir flesh message --}}
                    <x-biz.flash-message />
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">Importar transações</h3>

                    <form action="{{ route('transactions.process.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4 space-y-1">
                            <x-label for="image">Escolha arquivo Excel com relatório de transações PagBank.</x-label>
                            <input type="file" name="file"
                                class="px-2 py-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            @error('file')
                                <div class="text-xs text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex items-center gap-2">
                            <x-button>Enviar</x-button>
                            <x-biz.link href="{{ route('transactions.index') }}">Cancelar</x-biz.link>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
