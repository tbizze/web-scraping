<?php

namespace App\Http\Controllers;

use App\Imports\TransactionImport;
use App\Models\Leitor;
use App\Models\Status;
use App\Models\TipoPgto;
use App\Models\Transaction;
use App\Services\ImportExcelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tp_pgto_id = $request->input('tp_pgto_id');
        $status_id = $request->input('status_id');
        $search = $request->input('search');

        $transactions = Transaction::with(['tipoPgto', 'status', 'leitor'])
            ->when(request('tp_pgto_id'), function ($q) use ($tp_pgto_id) {
                return $q->where('tipo_pgto_id', '=', $tp_pgto_id);
            })
            ->when(request('status_id'), function ($q) use ($status_id) {
                return $q->where('status_id', '=', $status_id);
            })
            ->when($search, function ($query, $value) {
                $query->where('ref_transacao', 'like', "%$value%");
                $query->orWhere('dt_transacao', 'like', "%$value%");
            })
            ->paginate(30);

        $a = collect(Status::all())->map(function ($item) {
            return [
                'value' => $item['id'],
                'label' => $item['short_description'],
            ];
        })->toArray();

        $tpPgtos = TipoPgto::select('id as value', 'description as label')->get();
        $leitors = Leitor::select('id as value', 'description as label')->get();
        $statuses =
            collect(Status::all())->map(function ($item) {
                return [
                    'value' => $item['id'],
                    'label' => $item['short_description'],
                ];
            })->toArray();

        return view('transaction.index', compact(
            'transactions',
            'tpPgtos',
            'statuses',
            'tp_pgto_id',
            'status_id',
            'search'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    // Método para exibir view de importação individual de um arquivo CSV o Excel.
    public function import()
    {
        return view('transaction.import');
    }

    // Método para processar importação individual de um arquivo CSV o Excel.
    public function processImport(Request $request)
    {
        //dd($request->all());
        $request->validate([
            //'file' => 'required|mimes:xlsx,xls,csv',
            'file' => 'required|mimetypes:text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        //dd($request->all());

        $xz = Excel::import(new TransactionImport, $request->file('file'));
        //dd(Transaction::all(), $xz);

        return redirect()->back()->with('success', 'Transações importadas com sucesso!');
    }

    // Método para varrer diretório a procura de arquivos, resgatando seu path.
    // Encontrando, tenta realizar importação.
    public function importAll()
    {
        // Definir o diretório base com as imagens de QR Code.
        $baseDir = storage_path('app/extratos');
        $folders = ['2024'];
        $imports = [];
        $qdeArquivos = 0;
        $rowsSaved = 0;
        $rowsIgnored = 0;

        //Instancia serviço de importação.
        $importExcelService = new ImportExcelService;

        // Percorrer as pastas definidas em $folders.
        foreach ($folders as $folder) {
            // Define o diretório base para percorrer.
            $path = $baseDir . '/' . $folder;

            // Verifica se existem arquivos.
            if (File::exists($path)) {

                // Lista todos os arquivos em $path.
                $files = File::files($path);

                // Itera sobre os arquivos e realiza a importação.
                foreach ($files as $file) {

                    // Obtêm os dados do arquivo na variável $data.
                    $data = Excel::toCollection(new TransactionImport, $path . '/' . $file->getFilename());

                    // Tenta a importação, enviando os dados obtidos do arquivo. Retorna um array com quantidade de linhas importadas e ignoradas.
                    $imports[] = $importExcelService->allImport($data);
                    $qdeArquivos++;
                }
            }
        }
        // Itera a variável de retorno, somando quantidade de linhas processadas.
        foreach ($imports as $item) {
            $rowsSaved += $item['rowsSaved'];
            $rowsIgnored += $item['rowsIgnored'];
        }
        // Prepara mensagem de retorno para interface.
        $message = "Processado $qdeArquivos arquivos. Importado $rowsSaved linhas com sucesso e ignorado $rowsIgnored linhas!";

        // Retornar mensagem.
        if ($rowsSaved > 0) {
            return redirect()->route('transactions.index')->with('success', $message);
        }
        return redirect()->route('transactions.index')->with('message', $message);
    }
}
