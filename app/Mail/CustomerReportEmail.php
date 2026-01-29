<?php

    namespace App\Mail;

    use App\CustomerReport;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Mail\Mailable;
    use Illuminate\Queue\SerializesModels;

    class CustomerReportEmail extends Mailable
    {
        use Queueable, SerializesModels;

        private $customerReport;

        /**
         * CustomerReportEmail constructor.
         * @param CustomerReport $customerReport
         */
        public function __construct(CustomerReport $customerReport)
        {
            $this->customerReport = $customerReport;
        }

        /**
         * Build the message.
         *
         * @return $this
         */
        public function build()
        {
            return $this->view('emails.copia-fine-cantiere')
                ->attach($this->customerReport->customer_pdf)
                ->with([
                    'customerReport' => $this->customerReport
                ]);
        }
    }
