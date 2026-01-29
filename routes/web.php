<?php

    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!  
    |
    */

    Route::get('/', 'HomeController@dashboard')->name('dashboard');
    Route::get('/logout', 'HomeController@logout')->name('user_logout');

    Route::post('/media/upload', 'MediaController@store')->name('storemedia');
    Route::post('/media/file-upload', 'MediaController@mediaFileUpload')->name('storefile');
    Route::post('/media/ordering', 'MediaController@mediaImageOrdering')->name('order_media');
   
    // autocompletamento
    Route::post('/utility/autocomplete', 'UtilityController@autocomplete')->name('autocomplete');
    
    // Routes per amministratori autenticati
    Route::middleware(['auth', 'isAdmin'])->group(function () {

        Route::get('/set-previous-year', 'HomeController@setDashboardYear')->name('set_dashboard_year');
        //fix per vecchie date
        Route::get('/set-previous-year2', 'HomeController@setDashboardYear2')->name('set_dashboard_year2');
        Route::get('/set-previous-year3', 'HomeController@setDashboardYear3')->name('set_dashboard_year3');

        // Routes cantieri
        Route::post('/building-sites', 'BuildingSiteController@store')->name('building-sites.store');
        Route::get('/building-sites/create', 'BuildingSiteController@create')->name('building-sites.create');
        Route::get('/building-sites/{buildingSite}/edit', 'BuildingSiteController@edit')->name('building-sites.edit');
        Route::patch('/building-sites/{buildingSite}', 'BuildingSiteController@update')->name('building-sites.update');
        Route::delete('/building-sites/{buildingSite}/destroy', 'BuildingSiteController@destroy')->name('building-sites.destroy');
        Route::post('/building-sites/{buildingSite}/close-bs', 'BuildingSiteController@closeBuildingSite')->name('close_building_site');
        Route::get('/building-sites/{buildingSite}/reports', 'BuildingSiteController@buildingSiteReports')->name('building_site_reports');
        Route::get('/building-sites/{buildingSite}/show-media', 'BuildingSiteController@showMediaFiles')->name('bs_show_media_files');
        Route::get('/redirect-to-building-sites', function () {
            return redirect('/building-sites');
        })->name('redirect.building-sites');//rotta x forzare redirect corretto dopo aver eliminato un cantiere
        

        // Routes dipendenti
        Route::get('/employees/sites-user', 'UserController@sites_user')->name('employees.sites-user');
        Route::get('employee/view_sites/{employee}', 'UserController@view_sites')->name('employees.view_sites');
        Route::resource('/employees', 'UserController');
        

        // Routes clienti
        Route::resource('/customers', 'CustomerController');
        Route::get('/customers/{customer}/reports', 'CustomerController@customerReports')->name('customer_reports');

        // Routes Macchinari
        Route::resource('/machinery', 'MachineryController');
        Route::get('/tools', 'MachineryController@indexTools')->name('tools_list');
        Route::get('/tools/create', 'MachineryController@createTool')->name('create_tool');
        Route::get('/tools/{machinery}/edit', 'MachineryController@editTool')->name('edit_tool');

        // Routes file media
        Route::delete('/media/{medium}', 'MediaController@destroy')->name('media.destroy');

        // Image tagging
        Route::post('/media/image-tagging', 'MediaController@storeImageTags')->name('store_image_tags');

        // Search
        Route::get('/search-result', 'HomeController@searchResults')->name('search_results');

        // Notes
        Route::get('/notes/{noteId}/view', 'NoteController@show')->name('view_note');
        Route::patch('/notes/{note}', 'NoteController@update')->name('update_note');
        Route::get('/notes/filter', 'NoteController@index')->name('notes_list');
        Route::delete('/notes/{note}', 'NoteController@destroy')->name('notes.destroy');

        // Routes rapportini
        Route::get('/reports/{report}/download-pdf', 'ReportController@downloadDailyPdfReport')->name('daily_report_pdf');
        Route::get('/reports/download-all-pdf', 'ReportController@downloadAllPdfReport')->name('all_report_pdf');
        Route::get('/reports/not-compiled', 'ReportController@notCompiledReport')->name('not_compiled_report');

        // Rapportini cliente
        Route::get('/customer-reports', 'CustomerReportController@index')->name('customer_report_list');
        Route::get('/customer-reports/{customerReport}/view', 'CustomerReportController@show')->name('view_customer_report');
        Route::get('/customer-reports/{customerReport}/edit', 'CustomerReportController@edit')->name('edit_customer_report');
        Route::get('/customer-reports/{customerReport}/download-pdf', 'CustomerReportController@downloadPdf')->name('cst_report_pdf');
        Route::get('/customer-reports/download-all-pdf', 'CustomerReportController@downloadAllPdf')->name('all_cst_report_pdf');
        Route::patch('/customer-reports/{customerReport}', 'CustomerReportController@update')->name('update_cst_report');
        Route::delete('/customer-reports/{customerReport}', 'CustomerReportController@destroy')->name('delete_cst_report');

        // Materiali
        Route::get('/materials', 'MaterialController@index')->name('material_list');

        // Delete report functionality
        Route::delete('/reports/{report}', 'ReportController@destroy')->name('delete_employee_report');

        // SAL
        Route::post('/sal', 'HomeController@generateSal')->name('generate_sal');
        
        // Lista lavori assegnati
        Route::get('/work-list', 'WorkController@index')->name('work_list');
        Route::post('/work-list/store', 'WorkController@store')->name('work_store');
    });


    // Routes per dipendenti autenticati
    Route::middleware(['auth'])->group(function () {
        // Lista cantieri deve essere disponibile a tutti gli utenti registrati
        Route::get('/building-sites', 'BuildingSiteController@index')->name('building-sites.index');
        Route::get('/building-sites/{buildingSite}', 'BuildingSiteController@show')->name('building-sites.show');
        Route::get('/building-sites/{buildingSite}/upload-media', 'BuildingSiteController@uploadMediaFiles')->name('bs_upload_media_fine_cantiere');

        // Download media file
        Route::get('/media/{medium}/download', 'MediaController@downloadFile')->name('download_media_file');

        // Image tagging (lettura)
        Route::get('/media/image-tagging/{media}', 'MediaController@imageTagging')->name('tag_image');

        // Customer reports
        Route::post('/reports/customer', 'CustomerReportController@store')->name('store_cst_report');
        Route::get('/reports/fine-cantiere/{buildingSite}', 'CustomerReportController@create')->name('foglio_fine_cantiere');

        // Rapportini capannone
        Route::get('/reports/create/capannone', 'ReportController@createCapannone')->name('create_internal_report');

        // Routes rapportini
        Route::get('/reports/{report}/view', 'ReportController@show')->name('show_report');
        Route::get('/reports/create/{buildingSite}', 'ReportController@create')->name('reports.create');
        Route::get('/reports/{report}/edit', 'ReportController@edit')->name('reports.update');
        Route::get('/reports/{report}/forceclose', 'ReportController@forceclose')->name('reports.forceclose');
        Route::patch('/reports/{report}', 'ReportController@update')->name('update_employee_report');
        Route::get('/reports/filter', 'ReportController@index')->name('report_list');
        Route::get('/reports/filter-user', 'ReportController@index')->name('report_list_user');
        Route::post('/reports', 'ReportController@store')->name('store_employee_report');
        Route::get('/reports/update/{report}', 'ReportController@edit');
        Route::patch('/reports/update/{report}', 'ReportController@update');
        Route::post('/reports/csv-export', 'ReportController@employeeHoursCsvExport')->name('csv_hour_export');

        // Note
        Route::post('/notes', 'NoteController@store')->name('store_note');
        Route::get('/notes/{noteId}/edit', 'NoteController@edit')->name('edit_note');
        
        // Search
        Route::get('/search-result', 'HomeController@searchResults')->name('search_results');

        // Lista lavori assegnati
        Route::get('/work-list/user', 'WorkController@user')->name('work_list_user');
    });


    Auth::routes(['register' => false]);

    URL::forceScheme('https');
