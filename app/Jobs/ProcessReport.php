<?php

    namespace App\Jobs;

    use App\Report;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Queue\SerializesModels;

    class ProcessReport implements ShouldQueue
    {
        use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        /**
         * @var Report
         */
        protected $report;

        /**
         * @var string|string
         */
        protected $directoryName;

        /**
         * ProcessReport constructor.
         * @param Report $report
         * @param string $directoryName
         */
        public function __construct(Report $report, string $directoryName)
        {
            $this->report = $report;

            $this->directoryName = $directoryName;
        }

        /**
         * Execute the job.
         *
         * @return void
         */
        public function handle()
        {
            createPdfReport($this->report, false, $this->directoryName);
        }
    }
