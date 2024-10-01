<?php

namespace App\Http\Controllers;

use App\Imports\TransactionImport;
use App\Models\Leitor;
use App\Models\Status;
use App\Models\TipoPgto;
use App\Models\Transaction;
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

        $tpPgtos = TipoPgto::all();
        $statuses = Status::all();
        $leitors = Leitor::all();

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
    public function import()
    {
        //$qr_codes = QrCode::paginate(30);
        return view('transaction.import');
    }
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
    // Encontrando, realiza importação.
    public function importAll()
    {
        // Definir o diretório base com as imagens de QR Code.
        $baseDir = storage_path('app/extratos');
        $folders = ['2024'];
        $receipts = [];

        // Percorrer as pastas e listar os QR Codes.
        foreach ($folders as $folder) {
            $path = $baseDir . '/' . $folder;
            if (File::exists($path)) {
                $files = File::files($path);

                foreach ($files as $file) {
                    $receipts[] = [
                        'nomePasta' => $folder,
                        'nomeArquivo' => $file->getFilename(),
                    ];
                    Excel::import(new TransactionImport, $path . '/' . $file->getFilename());
                }
            }
        }
        //dd($receipts);
        return redirect()->route('transactions.index')->with('success', 'Transações importadas com sucesso!');
    }
}
