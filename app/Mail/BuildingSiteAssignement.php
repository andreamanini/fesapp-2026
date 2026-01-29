<?php

    namespace App\Mail;

    use App\BuildingSite;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Mail\Mailable;
    use Illuminate\Queue\SerializesModels;

    class BuildingSiteAssignement extends Mailable
    {
        use Queueable, SerializesModels;

        /**
         * @var BuildingSite
         */
        private $buildingSite;

        /**
         * @var bool
         */
        private $isManager;

        /**
         * Create a new message instance.
         *
         * @param BuildingSite $buildingSite
         * @param bool $isManager
         */
        public function __construct(BuildingSite $buildingSite, bool $isManager = null)
        {
            $this->buildingSite = $buildingSite;

            $this->isManager = (null !== $isManager ? true : false);
        }

        /**
         * Build the message.
         *
         * @return $this
         */
        public function build()
        {
            return $this->view('emails.bs-user-assignment')
                ->subject('Sei stato assegnato ad un nuovo cantiere')
                ->with([
                    'buildingSite' => $this->buildingSite,
                    'isManager' => $this->isManager
                ]);
        }
    }
