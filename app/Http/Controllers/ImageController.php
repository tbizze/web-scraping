<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $output = shell_exec("node " . base_path('convert.js') . " " . storage_path('app/' . $path));
        return view('result', ['text' => $output]);
    }
}
