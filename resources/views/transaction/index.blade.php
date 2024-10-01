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
                    <x-flash-message />

                    <div class="flex gap-2">
                        <a href="{{ route('transactions.import') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Importar Excel
                        </a>
                        <a href="{{ route('transactions.import-all') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Varrer Diretório
                        </a>

                        <form action="{{ route('transactions.index') }}" method="GET">
                            <div class="flex flex-wrap -mx-3 gap-2">
                                @csrf
                                <div class="">
                                    <select
                                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        id="tp_pgto_id" name="tp_pgto_id">
                                        <option selected value="">Tipo Pgto:</option>
                                        @foreach ($tpPgtos as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == $tp_pgto_id ? 'selected' : '' }}>
                                                {{ $item->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="">
                                    <select
                                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        id="status_id" name="status_id">
                                        <option selected value="">Status:</option>
                                        @foreach ($statuses as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == $status_id ? 'selected' : '' }}>
                                                {{ substr($item->description, 0, 20) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-button>Filtrar</x-button>

                            </div>
                        </form>

                    </div>

                    <table class="min-w-full w-full table-auto">
                        <thead>
                            <tr class="bg-slate-600 ">
                                <th class="">Tipo</th>
                                <th class="">Status</th>
                                <th class="">Bruto</th>
                                <th class="">Taxa</th>
                                <th class="">Líq.</th>
                                <th class="">Dt. Trans.</th>
                                <th class="">Dt. Comp.</th>
                                <th class="">Parcela</th>
                                <th class="">Ref.</th>
                                <th class="">Cód. Venda</th>
                                <th class="">Leitor</th>
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
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->valor_bruto }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->valor_taxa }}
                                    </td>
                                    <td class="px-4 py-2 ">
                                        {{ $item->valor_liquido }}
                                    </td>
                                    <td class="px-4 py-2 ">
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
