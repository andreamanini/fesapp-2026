<?php

    namespace App\Http\Controllers;

    use App\Machinery;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;

    class MachineryController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function index()
        {
            $machinery = Machinery::where('machine_type', '=', 'vehicle')
                ->orderBy('machine_name', 'asc')
                ->orderBy('machine_number', 'asc')
                ->get();

            return view('backend.machinery.machinery-list', compact('machinery'));
        }

        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function indexTools()
        {
            $tools = Machinery::where('machine_type', '=', 'tool')
                ->orderBy('machine_name', 'asc')
                ->orderBy('machine_number', 'asc')
                ->get();

            return view('backend.tools.tool-list', compact('tools'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            return view('backend.machinery.add-machine');
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function createTool()
        {
            return view('backend.tools.add-tool');
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function store(Request $request)
        {
            $request->validate([
                'machine_name' => 'required',
                'machine_type' => 'required|in:tool,vehicle',
            ]);

            // Validate if the user is authorised to perform this action
            if ($this->authorize('create', Machinery::class)) {

                // Insert the machinery
                try {
                    $machine = Machinery::create([
                        'machine_name' => $request->input('machine_name'),
                        'machine_number' => $request->input('machine_number'),
                        'machine_description' => $request->input('machine_description'),
                        'machine_type' => $request->input('machine_type'),
                        'created_by' => auth()->user()->name . ' ' . auth()->user()->surname,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Errore durante la creazione di un mezzo: '. $e->getMessage());
                    abort(500);
                }
            }

            if ('vehicle' == $request->input('machine_type')) {
                return redirect()->route('machinery.index');
            } else {
                return redirect()->route('tools_list');
            }
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  \App\Machinery $machinery
         * @return \Illuminate\Http\Response
         */
        public function edit(Machinery $machinery)
        {
            // Get any media linked to this machinery
            $machineryMedia = $machinery->media()->get();

            return view('backend.machinery.add-machine', compact('machinery', 'machineryMedia'));
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  \App\Machinery $machinery
         * @return \Illuminate\Http\Response
         */
        public function editTool(Machinery $machinery)
        {
            // Get any media linked to this machinery
            $machineryMedia = $machinery->media()->get();

            return view('backend.tools.add-tool', compact('machinery', 'machineryMedia'));
        }

        /**
         * Update the specified resource in storage.
         *
         * @param Request $request
         * @param Machinery $machinery
         * @return \Illuminate\Http\RedirectResponse
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function update(Request $request, Machinery $machinery)
        {
            $request->validate([
                'machine_name' => 'required',
                'machine_type' => 'required|in:tool,vehicle',
            ]);

            // Validate if the user is authorised to perform this action
            if ($this->authorize('create', Machinery::class)) {

                //  Update the machinery
                try {
                    $machinery->machine_name = $request->input('machine_name');

                    $machinery->machine_number = $request->input('machine_number');

                    $machinery->machine_description = $request->input('machine_description');

                    $machinery->machine_type = $request->input('machine_type');

                    $machinery->updated_by = auth()->user()->name . ' ' . auth()->user()->surname;

                    $machinery->save();

                } catch (\Exception $e) {
                    Log::error('Errore durante l\'aggiornamento di un mezzo: '. $e->getMessage());
                    abort(500);
                }
            }

            if ('vehicle' == $request->input('machine_type')) {
                return redirect()->route('machinery.index');
            } else {
                return redirect()->route('tools_list');
            }
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param Machinery $machinery
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function destroy(Machinery $machinery)
        {
            // Validate if the user is authorised to perform this action
            if ($this->authorize('delete', $machinery)) {

                try {

                    // We should remove the machinery id from all those building sites that are using it
                    DB::table('building_site_machinery')
                        ->where('machinery_id', '=', $machinery->id)
                        ->delete();


                    // Delete the machine
                    $machinery->delete();

                } catch (\Exception $e) {
                    Log::error('Errore durante l\'eliminazione di un mezzo: '. $e->getMessage());
                    abort(500, $e->getMessage());
                }
            }
        }
    }
