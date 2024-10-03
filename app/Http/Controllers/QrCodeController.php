<?php

namespace App\Http\Controllers;

use App\Exports\QrCodeExport;
use App\Exports\QrCodeQuitadoExport;
use App\Models\QrCode;
use App\Models\Transaction;
use App\Services\ConvertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class QrCodeController extends Controller
{
    // Método para listar os QR Codes salvos no BD.
    public function index(Request $request)
    {
        $grupo = $request->input('grupo');
        $search = $request->input('search');

        $qr_codes = QrCode::query()
            ->when(request('grupo'), function ($q) use ($grupo) {
                return $q->where('grupo', 'like', $grupo);
            })
            ->when(
                $search,
                function ($query, $value) {
                    $query->where('pagseguro_id', 'like', "%$value%");
                    $query->orWhere('carne', 'like', "$value");
                }
            )
            ->paginate(30);

        $grupos = [
            ['value' => 100, 'label' => 'Carnês de 100'],
            ['value' => 50, 'label' => 'Carnês de 50'],
            ['value' => 30, 'label' => 'Carnês de 30'],
            ['value' => 20, 'label' => 'Carnês de 20'],
        ];

        return view('scraping.index', compact('qr_codes', 'grupos', 'grupo', 'search'));
    }

    // Método para listar os QR Codes quitados no BD.
    public function listBaixados(Request $request)
    {
        $grupo = $request->input('grupo');
        $search = $request->input('search');

        $qr_codes = Transaction::query()
            ->with('qr_code')
            ->has('qr_code')
            ->withAggregate('qr_code', 'carne')
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
            ->orderBy('qr_code_carne')
            ->orderBy('dt_transacao')
            ->paginate(30);

        $grupos = [
            ['value' => 100, 'label' => 'Carnês de 100'],
            ['value' => 50, 'label' => 'Carnês de 50'],
            ['value' => 30, 'label' => 'Carnês de 30'],
            ['value' => 20, 'label' => 'Carnês de 20'],
        ];
        return view('scraping.baixados', compact('qr_codes', 'grupos', 'grupo', 'search'));
    }

    // Método para listar transações PIX quitados, mas não conseguiu relacionar com QR Codes.
    public function listBaixadosNotRelation(Request $request)
    {
        $grupo = $request->input('grupo');
        $search = $request->input('search');

        $qr_codes = Transaction::query()
            ->where('tipo_pgto_id', '=', 1) // 1=Pix.
            ->where('leitor_id', '=', null) // null = não foi Pix da maquininha.
            ->doesntHave('qr_code') // Não tem relacionamento com QrCode.
            ->when(
                $search,
                function ($query, $value) {
                    $query->where('ref_transacao', 'like', "%$value%");
                    $query->orWhere('dt_transacao', 'like', "%$value%");
                }
            )
            ->orderBy('dt_transacao')
            ->paginate(30);

        $grupos = [
            ['value' => 100, 'label' => 'Carnês de 100'],
            ['value' => 50, 'label' => 'Carnês de 50'],
            ['value' => 30, 'label' => 'Carnês de 30'],
            ['value' => 20, 'label' => 'Carnês de 20'],
        ];
        return view('scraping.baixados-not-relation', compact('qr_codes', 'grupos', 'grupo', 'search'));
    }

    // Método para varrer transactions e QR Codes, tentando relacioná-los.
    public function makeRelationshipTransactions()
    {
        // Busca todos os QR Codes salvos no BD.
        $qr_codes = QrCode::all();
        $i = 0;
        $y = 0;
        $z = 0;
        $relationship_transactions = [];

        foreach ($qr_codes as $qr_code) {
            // Busca transações filtrando pelo 'pagseguro_id'.
            $transactions = Transaction::where('ref_transacao', '=', $qr_code->pagseguro_id)->get();

            // Se existir transação.
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    // Monta um objeto juntando dados de QR Code e de Transaction.
                    $relationship_transactions[] = [
                        'transaction_id' => $transaction->id,
                        // 'qr_code_id' => $qr_code->id,
                        'carne' => $qr_code->carne,
                        // 'grupo' => $qr_code->grupo,
                        // 'pagseguro_id' => $qr_code->pagseguro_id,
                        // 'ref_transacao' => $transaction->ref_transacao,
                        'dt_transacao' => $transaction->dt_transacao,
                        // 'valor_bruto' => $transaction->valor_bruto,
                        // 'valor_liquido' => $transaction->valor_liquido,
                    ];
                    $i++;

                    // Verifica se não existe uma relação entre QR Code e Transação.
                    if (!$transaction->qr_code_id) {

                        // Salvar relacionamento entre QR Code e Transação.
                        //$transaction->update(['qr_code_id' => $transaction->qr_code_id]);
                        $transaction->qr_code()->associate($qr_code);
                        $transaction->save();
                        $z++;
                    }
                    //dd($transaction);
                }
            }
            $y++;
        }
        if ($z > 0) {
            return redirect()->route('comprovantes.baixado')->with('success', "Foram relacionados $z transações com sucesso!");
        } else {
            return redirect()->route('comprovantes.baixado')->with('message', "Nenhuma relação entre Transação e QrCode foi encontrada para salvar!");
        }
    }

    // Método para varrer diretórios, obtendo informações dos arquivos de QRCode.
    function scanPastas()
    {
        // Definir o diretório base com as imagens de QR Code.
        $baseDir = storage_path('app/images');
        $folders = ['100', '50', '30', '20'];
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
                }
            }
        }

        // Salvar os QR Codes no BD e redirecionar para a página de listagem.
        $stored = $this->storeQrCodes($receipts);
        if ($stored['items'] == 0) {
            return redirect()->route('comprovantes.index')->with('message', 'Nenhum arquivo QR Code encontrado!');
        } else {
            return redirect()->route('comprovantes.index')->with('success', $stored['message']);
        }
    }

    // Método para salvar as informações dos QRCode no BD.
    public function storeQrCodes($receipts)
    {
        $items = [];
        $qdeArquivos = 0;
        foreach ($receipts as $receipt) {
            $items[] = [
                'path' => 'app/images/' . $receipt['nomePasta'],
                'name_file' => $receipt['nomeArquivo'],
                'grupo' => $receipt['nomePasta'],
                'carne' => $this->getNameCarne($receipt['nomeArquivo']),
            ];
            $qdeArquivos++;
        }

        $saveArquivos = 0;
        $ignoreArquivos = 0;
        // Salvar os QR Codes no banco de dados
        foreach ($items as $item) {
            // Procura registro com as informações do arquivo escaneado.
            $qrCode = QrCode::query()
                ->where('grupo', '=', $item['grupo'])
                ->where('carne', '=', $item['carne'])
                ->get();

            // Se não existe um registro com os mesmos dados, cria.
            if ($qrCode->isEmpty()) {
                $saveArquivos++;
                QrCode::create($item);
            } else {
                $ignoreArquivos++;
            }
        }
        // Prepara mensagem de retorno para interface.
        $message = "Salvo $saveArquivos arquivos com sucesso e ignorado $ignoreArquivos arquivos!";

        // Retornar a quantidade de QR Codes salvos e uma mensagem.
        return ['items' => $qdeArquivos, 'message' => $message];
    }

    // Método faz conversão de JPG para TEXT.
    public function convert(QrCode $qrCode)
    {
        // Se o QR Code já possui identificador, então sai do método.
        if ($qrCode->status == 1) {
            // Retornar mensagem informando.
            return redirect()->route('comprovantes.index')->with('message', 'Este QR Code já possui identificador salvo.');
        }


        // Monta a url para a imagem.
        $imagePath = storage_path($qrCode->path . '/' . $qrCode->name_file);

        // Faz o web scraping para converter a imagem para TXT.
        $output = shell_exec("node " . base_path('scrap-convert-ocr.cjs') . " " . $imagePath);

        // Se há resultado, salvar os QR codes no banco de dados
        if ($output) {

            // Limpar e formatar os dados.
            $linha = $this->cleanData($output);

            // Salvar informações no banco de dados
            $qrCode->update([
                'content' => $linha,
                'status' => 1,
                'pagseguro_id' => $linha['id'],
            ]);

            // Retornar mensagem informando.
            return redirect()->route('comprovantes.index')->with('success', 'Este QR foi convertido com sucesso!');
        } else {

            // Retornar mensagem informando.
            return redirect()->route('comprovantes.index')->with('error', 'Erro ao converter a imagem para TXT.');
        }
    }



    public function listaConvert()
    {
        // Busca o registro com dados da imagem.
        $qrCodes = QrCode::query()
            ->where('status', '=', 0)
            ->limit(10)
            ->get();

        dump($qrCodes->toArray());

        $results = [];
        foreach ($qrCodes as $item) {
            // Invoca método para conversão
            $data = $this->convert($item->id);

            if ($data) {
                // Prepara dados.
                $result = [
                    'id' => $item->id,
                    'carne' => $item->name_file,
                    'grupo' => $item->path,
                ];
                // Adicionar resultado ao array.
                $results = array_merge($results, $result);
            }
        }

        dd($results);
    }

    // Função para pegar o nome do carnê a partir do nome do arquivo.
    public function getNameCarne(string $name)
    {
        $carne = explode("-", $name);
        return $carne[0];
    }

    // Função para limpar e formatar os dados
    function cleanData($data)
    {
        $linhas = explode("\n", trim($data));
        $dados = explode(" ", $linhas[4]);

        return [
            'desc' => $dados[3],
            'id' => $dados[5],
        ];
    }


    public function export()
    {
        return Excel::download(new QrCodeExport, 'qr-codes.xlsx');
    }

    // Método para exportar em Excel os comprovantes baixados.
    public function baixadosExport(Request $request)
    {
        //dd('test', $request->all());
        //return Excel::download(new QrCodeExport, 'qr-codes-baixado.xlsx');

        return (new QrCodeQuitadoExport(2023))->download('qr-codes-baixado.xlsx');
    }
}
