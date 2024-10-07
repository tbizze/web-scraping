<?php

namespace App\Http\Controllers;

use App\Imports\PessoaImport;
use App\Models\Pessoa;
use App\Models\QrCode;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $grupo = $request->input('grupo');
        $search = $request->input('search');

        $pessoas = Pessoa::query()
            //->with('qr_code')
            //->has('qr_code')
            //->withAggregate('qr_code', 'carne')
            ->when(
                $search,
                function ($query, $value) {
                    $query->where('ref_transacao', 'like', "%$value%");
                    $query->orWhere('dt_transacao', 'like', "%$value%");
                }
            )
            // ->when(request('grupo'), function ($q) use ($grupo) {
            //     return $q->whereHas('grupo', 'like', $grupo);
            // })
            ->orderBy('nome')
            ->orderBy('notas')
            ->paginate(30);

        $grupos = [
            ['value' => 100, 'label' => 'Carnês de 100'],
            ['value' => 50, 'label' => 'Carnês de 50'],
            ['value' => 30, 'label' => 'Carnês de 30'],
            ['value' => 20, 'label' => 'Carnês de 20'],
        ];
        return view('pessoa.index', compact('pessoas', 'grupos', 'grupo', 'search'));
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
    public function show(Pessoa $pessoa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pessoa $pessoa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pessoa $pessoa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pessoa $pessoa)
    {
        //
    }

    // Método para exibir view de importação individual de um arquivo CSV o Excel.
    public function import()
    {
        return view('pessoa.import');
    }

    // Método para processar importação individual de um arquivo CSV o Excel.
    public function processImport(Request $request)
    {
        $request->validate([
            //'file' => 'required|mimes:xlsx,xls,csv',
            'file' => 'required|',
        ]);

        // Importar dados da planilha e salvar na base de dados.
        $xz = Excel::import(new PessoaImport, $request->file('file'));

        return redirect()->back()->with('success', 'Transações importadas com sucesso!');
    }

    // Método para varrer Pessoas e QR Codes, tentando relacioná-los.
    public function makeRelationshipQrCodes()
    {
        // Busca todos as Pessoas.
        $pessoas = Pessoa::all();
        $i = 0;
        $y = 0;
        $z = 0;
        $relationship_qr_codes = [];

        foreach ($pessoas as $pessoa) {
            // Busca QrCodes filtrando pelo 'carne'.
            $qr_codes = QrCode::where('carne', '=', $pessoa->notas)->orderBy('carne', 'asc')->get();

            // Se existir qr_codes.
            if ($qr_codes) {
                foreach ($qr_codes as $qr_code) {
                    $relationship_qr_codes[] = [
                        'pessoa_id' => $pessoa->id,
                        'qr_code_id' => $qr_code->id,
                        'carne' => $qr_code->carne,
                        //'grupo' => $qr_code->grupo,
                        //'pagseguro_id' => $qr_code->pagseguro_id,
                        'notas' => $pessoa->notas,
                        //'dt_transacao' => $transaction->dt_transacao,
                        // 'valor_bruto' => $transaction->valor_bruto,
                        // 'valor_liquido' => $transaction->valor_liquido,
                    ];
                    $i++;

                    // Verifica se não existe uma relação entre Pessoa e QR Code.
                    if (!$qr_code->pessoa_id) {
                        // Adiciona a relação entre QR Code e Transação.
                        $qr_code->pessoa_id = $pessoa->id;
                        $qr_code->save();
                        $z++;
                    }
                    //dd($qr_code);
                }
            }
            $y++;
        }
        if ($z > 0) {
            return redirect()->route('pessoas.index')->with('success', "Foram relacionados $z pessoas com sucesso!");
        } else {
            return redirect()->route('pessoas.index')->with('message', "Nenhuma relação entre Pessoa e QrCode foi encontrada para salvar!");
        }
    }

    // Método para varrer Pessoas e corrigir o código do carnê formatando
    public function correctCarne()
    {
        // Busca todos as Pessoas.
        $pessoas = Pessoa::all();
        $i = 0;
        $y = 0;

        $carnes = [];

        foreach ($pessoas as $pessoa) {
            // Se o código do carnê não está formatado.
            if (strlen($pessoa->notas) < 3) {
                $carnes[] = [
                    'notas' => $pessoa->notas,
                    'carne_correct' => str_pad($pessoa->notas, 3, '0', STR_PAD_LEFT),
                ];

                // Adiciona zeros à esquerda do código do carnê.
                $pessoa->notas = str_pad($pessoa->notas, 3, '0', STR_PAD_LEFT);
                //$pessoa->save();
                $i++;
            }
            $y++;
        }
        dd($i, $y, $carnes);
        if ($i > 0) {
            return redirect()->route('pessoas.index')->with('success', "Foram corrigidos $i códigos de carnê com sucesso!");
        } else {
            return redirect()->route('pessoas.index')->with('success', "Foram corrigidos $i códigos de carnê com sucesso!");
        }
    }
}
