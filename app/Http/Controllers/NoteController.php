<?php

    namespace App\Http\Controllers;

    use App\Note;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Gate;
    use Illuminate\Support\Facades\Log;

    class NoteController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @param Request $request
         * @param string|null $startDate
         * @param string|null $endDate
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function index(Request $request)
        {
            $startDate = $request->get('date_from');
            $endDate = $request->get('date_to');

            // Display all the stats based on the month selected (if any)
            if (null !== $startDate) {
                $sdArray = explode('-', $startDate);
                $dateFrom = new Carbon(date("{$sdArray[2]}-{$sdArray[1]}-{$sdArray[0]} 00:00:00"));
            } else {
                $dateFrom = new Carbon(date("Y-m-01 00:00:00"));
            }


            if (null !== $endDate) {
                $edArray = explode('-', $endDate);
                $dateTo = new Carbon(date("{$edArray[2]}-{$edArray[1]}-{$edArray[0]} 23:59:59"));
            } else {
                $dateTo = new Carbon(date("Y-m-t 23:59:59"));
            }

            $notes = Note::whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('id', 'desc')
                ->get();

            return view('backend.notes.note-list', compact('notes', 'dateFrom', 'dateTo'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            //
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            $request->validate([
                'body' => 'required|min:10',
                'building_site_id' => 'required|exists:building_sites,id',
            ]);

            $geolocation = [
                'latitude' => $request->input('location_lat'),
                'longitude' => $request->input('location_lng'),
            ];

            try {
                 $note = Note::create([
                     'body' => $request->input('body'),
                     'building_site_id' => $request->input('building_site_id'),
                     'geotagging' => json_encode($geolocation),
                     'user_id' => auth()->user()->id,
                     'created_by' => auth()->user()->name . ' '. auth()->user()->surname
                 ]);
            } catch (\Exception $e) {
                Log::error('Errore durante la creazione di una nota: '. $e->getMessage());
                abort(500, $e->getMessage());
            }

            // Setup a session variable to allow image uploads
            session()->flash('allow_notes_img_upload', true);

            return redirect()->route('edit_note', $note->id);
        }

        /**
         * Display the specified resource.
         *
         * @param  \App\Note $noteId
         * @return \Illuminate\Http\Response
         */
        public function show(Note $noteId)
        {
            $note = $noteId;
            return view('backend.notes.edit-note', compact('note'));
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  \App\Note $noteId
         * @return \Illuminate\Http\Response
         */
        public function edit(Note $noteId)
        {
            /**
             * N.B. Non inserire controllo authorize per update in quanto questa funzione viene usata sia da employee
             * che da super admin
            */
            $note = $noteId;
            $enableSuperAdminEdit = Gate::allows('update', $note);
            return view('backend.notes.edit-note', compact('note', 'enableSuperAdminEdit'));
        }

        /**
         * Updates a specific note
         *
         * @param Note $note
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function update(Request $request, Note $note)
        {
            // Check for authorization as only super admin should update notes
            if ($this->authorize('update', $note)) {

                try {
                    $note->body = $request->input('body');

                    $note->updated_by = auth()->user()->name . ' '. auth()->user()->surname;

                    $note->save();

                } catch (\Exception $e) {
                    Log::error('Errore durante la modifica di una nota: '. $e->getMessage());
                    abort(500, $e->getMessage());
                }

                return redirect()->route('notes_list');
            }
        }

        /**
         * Function used to delete a note
         *
         * @param Note $note
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function destroy(Note $note)
        {
            if ($this->authorize('delete', $note)) {

                try {
                    $ts = Carbon::now()->toDateTimeString();
                    $data = array('deleted_at' => $ts, 'updated_by' => auth()->user()->name . ' ' . auth()->user()->surname);
                    $note->update($data);

                } catch (\Exception $e) {
                    Log::error('Errore durante l\'eliminazione di una nota: '. $e->getMessage());
                    abort(500, $e->getMessage());
                }
            }
        }

    }
