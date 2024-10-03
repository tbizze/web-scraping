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
                            <x-biz.link href="{{ route('comprovantes.make-relationship') }}">
                                Buscar relacionamentos
                            </x-biz.link>
                            <x-biz.link href="{{ route('comprovantes.baixado-export') }}">
                                Exportar em Excel
                            </x-biz.link>

                        </div>
                        <div class="flex">
                            <x-biz.link href="{{ route('comprovantes.baixado-not-relation') }}">
                                Não relacionados
                            </x-biz.link>
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
                                <th class="">Grupo</th>
                                <th class="">Carnê</th>
                                <th class="">Id Pagbank</th>
                                <th class="">Dt. Baixa</th>
                                <th class="">Bruto</th>
                                <th class="">Taxa</th>
                                <th class="">Líquido</th>
                                <th class="">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($qr_codes as $item)
                                <tr class="text-sm border-b ">
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->id }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->qr_code->grupo }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->qr_code->carne }}
                                    </td>
                                    <td class="px-4 py-2 text-left">
                                        {{ $item->ref_transacao }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->dt_compensacao }}
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        {{ $item->valor_bruto }}
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        {{ $item->valor_taxa }}
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        {{ $item->valor_liquido }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->status->description ? $item->status->description : 'Em aberto' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $qr_codes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
