<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use League\Flysystem\Util;

use TCG\Voyager\Http\Controllers\VoyagerController as Voyager;

class VoyagerController extends Voyager
{
    public function logout()
    {
        Auth::logout();
        
        return redirect()->route('login');
    }
}
