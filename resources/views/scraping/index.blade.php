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
                    <x-flash-message />

                    <div class="flex gap-2">
                        <a href="{{ route('ocr.lsqrcode') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Processar arquivos
                        </a>
                        <a href="{{ route('ocr.export') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Exportar Excel
                        </a>

                    </div>
                    <table class="min-w-full w-full table-auto">
                        <thead>
                            <tr class="bg-slate-600 ">
                                <th class="">ID</th>
                                <th class="">Grupo</th>
                                <th class="">Carnê</th>
                                <th class="">Id Pagbank</th>
                                <th class="">Caminho</th>
                                <th class="">Arquivo</th>
                                <th class="">Corpo</th>
                                <th class="">Status</th>
                                <th class="">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($qr_codes as $item)
                                <tr class="text-sm border-b ">
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->id }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->grupo }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->carne }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->pagseguro_id }}
                                    </td>
                                    <td class="px-4 py-2 ">
                                        {{ $item->path }}
                                    </td>
                                    <td class="px-4 py-2 ">
                                        {{ $item->name_file }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->content }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $item->status }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <a href="{{ route('ocr.convert', $item->id) }}"
                                            class="px-1 py-1 bg-slate-500 rounded text-xs uppercase">
                                            Converte
                                        </a>
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
