<?php

namespace App\Http\Controllers;

use App\Work;
use App\User;
use App\BuildingSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // Lista dei dipendenti
        $user = new User();
        $employees = $user->getActualEmployeeList();
        
        $buildingSites = BuildingSite::where('status', '=', "open")->orderBy('building_sites.site_name', 'asc')->get();
        
        return view('backend.work.work-list',compact('employees','buildingSites'));
    }
    
    
    public function user() {
        $user = new User();
        $employees = $user->getActualEmployeeList(auth()->user()->id);
        $employee = $employees[0];
        $buildingSites = BuildingSite::where('status', '=', "open")->orderBy('building_sites.site_name', 'asc')->get();

        return view('backend.work.work-list-user',compact('employee','buildingSites'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $users = $request->get('users_id');
        $allfield = $request->all();
        
        Work::truncate();

        $modifiedUsers = []; // Array per tenere traccia degli utenti modificati
        
        foreach ($users as $user) {
            
            $data = array();
            foreach($allfield AS $k=>$v) {
                if(preg_match("/^".$user."_(.*)$/", $k, $matches)) {
                    $data[$matches[1]] = $v;
                }
            }

            list($day,$month,$year) = explode("-",$data['date']);
            
            Work::create([
                'user_id' => $user,
                'building_site_id' => $data['building_site_id'],
                'date' => $year."-".$month."-".$day,
                'time' => $data['time'].":00",
                'truck_no' => $data['truck_no'],
                'work_description' => $data['work_description'],
                'created_by' => auth()->user()->name . ' ' . auth()->user()->surname
            ]);
            
            DB::table('building_site_user')->insertOrIgnore([
                ['building_site_id' => $data['building_site_id'], 'user_id' => $user]
            ]);

            // Aggiungi l'utente alla lista degli utenti modificati
            $modifiedUsers[] = $user;
        }

        // Aggiorna il campo 'work_notified' per gli utenti modificati
        User::whereIn('id', $modifiedUsers)->update(['work_notified' => 0]);
        
        return redirect()->route('work_list');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Work  $work
     * @return \Illuminate\Http\Response
     */
    public function show(Work $work)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Work  $work
     * @return \Illuminate\Http\Response
     */
    public function edit(Work $work)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Work  $work
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Work $work)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Work  $work
     * @return \Illuminate\Http\Response
     */
    public function destroy(Work $work)
    {
        //
    }
}
