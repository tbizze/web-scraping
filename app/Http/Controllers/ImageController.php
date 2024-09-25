<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function index()
    {
        return view('scraping.upload');
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

    public function notices()
    {

        //dd('notices');
        $output = shell_exec("node " . base_path('test.cjs'));
        // foreach ($output as $item) {
        //     Log::info('Notificação: '. $item);
        // }
        dd($output);

        return view('scraping.result', ['text' => $output]);
    }

    // Função para limpar e formatar os dados
    function cleanData($data)
    {
        return array_map(function ($item) {
            return [
                'url' => trim($item['url']),
                'title' => trim($item['title']),
                'description' => trim($item['description'])
            ];
        }, $data);
    }
}
