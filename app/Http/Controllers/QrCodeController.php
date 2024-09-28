<?php

namespace App\Http\Controllers;

use App\Exports\QrCodeExport;
use App\Models\QrCode;
use App\Services\ConvertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class QrCodeController extends Controller
{
    // Método para listar os QR Codes salvos no BD.
    public function index()
    {
        $qr_codes = QrCode::paginate(30);
        return view('scraping.index', compact('qr_codes'));
    }

    // Método para varrer diretórios, obtendo informações dos arquivos de QRCode.
    function scanQrReceipts()
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
        $this->storeQrCodes($receipts);
        return redirect()->route('ocr.index')->with('success', 'Imagens listadas com sucesso!');
    }

    // Método para salvar as informações dos QRCode no BD.
    public function storeQrCodes($receipts)
    {
        $items = [];
        $id = 0;
        foreach ($receipts as $receipt) {
            $items[] = [
                'path' => 'app/images/' . $receipt['nomePasta'],
                'name_file' => $receipt['nomeArquivo'],
                'grupo' => $receipt['nomePasta'],
                'carne' => $this->getNameCarne($receipt['nomeArquivo']),
            ];
            $id++;
        }

        $x = 0;
        // Salvar os QR Codes no banco de dados
        foreach ($items as $item) {
            $test = QrCode::query()
                ->where('grupo', '=', $item['grupo'])
                ->where('carne', '=', $item['carne'])
                ->get();

            // Verifica se já existe um registro com os mesmos dados.
            if ($test->isEmpty()) {
                $x++;
                //QrCode::create($item);
            }

            //QrCode::create($item);
        }

        // Retornar a quantidade de QR Codes salvos e uma mensagem de sucesso.
        return ['items' => $id, 'message' => 'Salvo com sucesso!'];
    }

    // Método faz conversão de JPG para TEXT.
    public function convert(string $id)
    {
        // Busca o registro com dados da imagem.
        $qrCode = QrCode::findOrFail($id);

        // Se o QR Code já possui identificador, então sai do método.
        if ($qrCode->status == 1) {
            // Retornar mensagem informando.
            return redirect()->route('ocr.index')->with('message', 'Este QR Code já possui identificador salvo.');
        }


        // Monta a url para a imagem.
        $imagePath = storage_path($qrCode->path . '/' . $qrCode->name_file);

        // Faz o web scraping para converter a imagem para TXT.
        $output = shell_exec("node " . base_path('convert2.cjs') . " " . $imagePath);

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
            return redirect()->route('ocr.index')->with('success', 'Este QR foi convertido com sucesso!');
        } else {

            // Retornar mensagem informando.
            return redirect()->route('ocr.index')->with('error', 'Erro ao converter a imagem para TXT.');
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
}
