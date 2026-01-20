<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanduanController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;
        $title = 'Panduan Aplikasi';
        
        // You can customize the URL or content based on the role if needed later.
        // For now, using the same flipbook for both as requested, 
        // but the code structure allows for separate ones easily.
        
        if ($role === 'peternak') {
            $title = 'Panduan Aplikasi (Peternak)';
            // $url = '...'; 
        } else {
            $title = 'Panduan Aplikasi (Pengelola & Admin)';
            // $url = '...';
        }

        return view('panduan.index', compact('title', 'role'));
    }
}
