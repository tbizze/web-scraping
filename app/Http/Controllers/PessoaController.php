<?php

namespace App\Http\Controllers;

use App\Imports\PessoaImport;
use App\Models\Pessoa;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
}
