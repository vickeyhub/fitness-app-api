<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function index(){
        $data['users'] = User::with('profile')->get();
        return view('admin.users.index', $data);
    }
}
