<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UtilityController extends Controller
{
    //
    
    public function autocomplete(Request $request)
    {
        if (Auth::check()) {
            $autocomplete = DB::table('autocompleteworksitetable')->select('name as value')->where('type', $request->input('type'))->where('name', 'like', "%".$request->input('term')."%")->get();
            return response()->json($autocomplete)->setCallback($request->input('callback'));
        }
    }
}
