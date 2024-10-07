<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Listar QR Codes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">


                <div class="mx-5 my-5 text-gray-900 dark:text-gray-100 space-y-3">

                    {{-- Chama compomente para exibir flesh message --}}
                    <x-biz.flash-message />

                    <div class="flex justify-between">
                        <div class="flex gap-2">
                            <x-biz.link href="{{ route('pessoas.make-relationship') }}">
                                Buscar relacionamentos
                            </x-biz.link>
                            <x-biz.link href="{{ route('comprovantes.baixado-export') }}">
                                Exportar em Excel
                            </x-biz.link>

                        </div>
                        <div class="flex">

                            <form action="{{ route('comprovantes.baixado') }}" method="GET" class="ml-2">
                                <div class="flex flex-wrap gap-2">
                                    @csrf
                                    <div class="">
                                        <x-biz.select id="grupo" name="grupo" :options="$grupos" :selected="$grupo"
                                            class="py-2">
                                            <option selected value="">Grupo:</option>

                                        </x-biz.select>
                                    </div>
                                    <div class="">
                                        <x-input id="search" name="search" value="{{ old('search', $search) }} "
                                            class="py-2" placeholder="Localizar..." />
                                    </div>
                                    <x-button>Filtrar</x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="min-w-full w-full table-auto">
                        <thead>
                            <tr class="bg-slate-600 ">
                                <th class="py-3">ID</th>
                                <th class="text-left">Nome</th>
                                <th class="">Dt. Nascimento</th>
                                <th class="">Telefone</th>
                                <th class="text-left">Notas</th>
                                <th class="">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pessoas as $item)
                                <tr class="text-sm border-b ">
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->id }}
                                    </td>
                                    <td class="px-4 py-2 text-left">
                                        {{ $item->nome }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->data_nascimento }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->telefone }}
                                    </td>
                                    <td class="px-4 py-2 text-left">
                                        {{ $item->notas }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->pessoaStatus->description ? $item->pessoaStatus->description : '' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $pessoas->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
