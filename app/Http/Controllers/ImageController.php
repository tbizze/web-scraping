<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function index()
    {
        $qr_codes = QrCode::all();
        return view('scraping.index', compact('qr_codes'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('image')->store('images');
        $imagePath = storage_path('app/' . $path);

        Log::info('Chamando o script Node.js com o caminho da imagem: ' . $imagePath);
        $output = shell_exec("node " . base_path('convert.cjs') . " " . $imagePath);
        Log::info('Resultado do script Node.js: ' . $output);

        dump($imagePath, $output);

        return view('scraping.result', ['text' => $output]);
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

        // Salvar os QR Codes no banco de dados
        foreach ($items as $item) {
            QrCode::create($item);
        }

        // Retornar a quantidade de QR Codes salvos e uma mensagem de sucesso.
        return ['items' => $id, 'message' => 'Salvo com sucesso!'];
    }

    // Função para pegar o nome do carnê a partir do nome do arquivo.
    public function getNameCarne(string $name)
    {
        $carne = explode("-", $name);
        return $carne[0];
    }

    public function getIdentificador(int $id)
    {
        $qrCode = QrCode::findOrFail($id);
        dump($qrCode->toArray());

        $content = $qrCode->content;

        $linhas = explode("\n", trim($content));
        $dados = explode(" ", $linhas[4]);
        $id = $dados[5];
        //dd($linhas, $linhas[4], $dados, $id);

        if ($id) {
            // Salvar os QR Codes no banco de dados
            $qrCode->update([
                'pagseguro_id' => $id,
                'status' => 2,
            ]);
        }
        dd($qrCode->toArray(), $id);
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

    public function notices()
    {

        //dump('notices: ' .  "node " . base_path('test.cjs'));
        $output = shell_exec("node " . base_path('scrap-notices.cjs'));
        // foreach ($output as $item) {
        //     Log::info('Notificação: '. $item);
        // }
        dd($output);

        return view('scraping.result', ['text' => $output]);
    }
}
