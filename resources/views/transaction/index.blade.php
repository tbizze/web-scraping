<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Listar Transações') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">


                <div class="mx-5 my-5 text-gray-900 dark:text-gray-100 space-y-3">

                    {{-- Chama compomente para exibir flesh message --}}
                    <x-biz.flash-message />

                    <div class="flex justify-between">
                        {{-- Lado Esquerdo: botões --}}
                        <div class="">
                            <div class="flex gap-2">
                                <x-biz.link href="{{ route('transactions.import-all') }}" class="py-3">
                                    Varrer Pastas
                                </x-biz.link>
                                <x-biz.link href="{{ route('transactions.import') }}" class="py-3">
                                    Importar
                                </x-biz.link>
                            </div>
                        </div>
                        {{-- Lado Direito: filtros --}}
                        <div class="">
                            <form action="{{ route('transactions.index') }}" method="GET" class="ml-4">
                                <div class="flex flex-wrap gap-2">
                                    @csrf
                                    <div class="">
                                        <x-biz.select id="tp_pgto_id" name="tp_pgto_id" :options="$tpPgtos"
                                            :selected="$tp_pgto_id" class="py-2">
                                            <option selected value="">Tipo Pgto:</option>
                                        </x-biz.select>
                                    </div>
                                    <div class="">
                                        <x-biz.select id="status_id" name="status_id" :options="$statuses"
                                            :selected="$status_id" class="py-2">
                                            <option selected value="">Status:</option>
                                        </x-biz.select>
                                    </div>
                                    <div class="">
                                        <x-input id="search" name="search" value="{{ old('search', $search) }}"
                                            placeholder="Localizar..." />
                                    </div>
                                    <x-button>Filtrar</x-button>
                                </div>
                            </form>
                        </div>
                    </div>


                    <table class="min-w-full w-full table-auto">
                        <thead>
                            <tr class="bg-slate-600 ">
                                <th class="py-3">Tipo</th>
                                <th class="">Status</th>
                                <th class="">Bruto</th>
                                <th class="">Taxa</th>
                                <th class="">Líq.</th>
                                <th class="">Dt. Trans.</th>
                                <th class="">Dt. Comp.</th>
                                <th class="">Parcela</th>
                                <th class="">Ref.</th>
                                <th class="">Cód. Venda</th>
                                <th class="pr-2">Leitor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $item)
                                <tr class="text-sm border-b ">
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->tipoPgto->description }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->status->description }}
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
                                        {{ $item->dt_transacao }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->dt_compensacao }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->parcelas }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->ref_transacao }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->cod_venda }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->leitor->description }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
