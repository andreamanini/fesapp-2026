<?php

    namespace App\Http\Controllers;

    use App\Material;
    use Illuminate\Http\Request;

    class MaterialController extends ApiController
    {

        public function index(Request $request)
        {
            return Material::orderBy('material_name', 'asc')
                ->where('material_name', 'like', "{$request->get('term')}%")
                ->get()
                ->pluck('material_name');
        }
    }
