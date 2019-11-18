<?php

namespace App\Http\Controllers\Voyager;

use TCG\Voyager\Http\Controllers\VoyagerController as Voyager;

class VoyagerController extends Voyager
{
    public function logout()
    {
        app('VoyagerAuth')->logout();

        //return redirect()->route('voyager.login');
        
        return redirect()->route('login');
    }
}
