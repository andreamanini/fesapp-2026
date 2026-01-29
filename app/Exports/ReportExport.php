<?php

    namespace App\Exports;

    use App\BuildingSite;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Contracts\View\View;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Maatwebsite\Excel\Concerns\FromView;

    class ReportExport implements FromView
    {

        use Exportable;

        /**
         * @var BuildingSite
         */
        protected $buildingSite;

        /**
         * @var Collection
         */
        protected $reports;


        /**
         * ReportExport constructor.
         * @param BuildingSite $buildingSite
         * @param Collection $reports
         */
        public function __construct(BuildingSite $buildingSite, Collection $reports)
        {
            $this->buildingSite = $buildingSite;
            $this->reports = $reports;
        }


        /**
         * @return View
         */
        public function view(): View
        {
            return view('backend.SAL.compilazione-lavori-consuntivo', [
                'reports' => $this->reports,
                'buildingSite' => $this->buildingSite
            ]);
        }
    }
