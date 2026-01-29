<?php

    namespace App\Http\Controllers;

    use Illuminate\Foundation\Bus\DispatchesJobs;
    use Illuminate\Foundation\Validation\ValidatesRequests;
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    class ApiController extends Controller
    {
        use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

        /**
         * @var int
         */
        protected $statusCode = 200;

        /**
         * @var int
         */
        protected $recordLimit = 25;


        /**
         * ApiController constructor.
         */
        public function __construct()
        {
//            $this->middleware('auth:api');
        }

        /**
         * @return int
         */
        public function getStatusCode()
        {
            return $this->statusCode;
        }

        /**
         * @param $statusCode
         * @return $this
         */
        public function setStatusCode($statusCode)
        {
            $this->statusCode = $statusCode;

            return $this;
        }

        /**
         * Generates a 201 json encoded status message
         *
         * @param string $message
         * @return mixed
         */
        public function respondCreated($message = 'The resource has been created')
        {
            return $this->setStatusCode(201)->respondWithError($message);
        }

        /**
         * Generates a 204 json encoded status message
         *
         * @param string $message
         * @return mixed
         */
        public function respondDeleted($message = 'The resource has been deleted')
        {
            return $this->setStatusCode(204)->respondWithError($message);
        }

        /**
         * Generates a 400 json encoded error message
         *
         * @param string $message
         * @param null $errors
         * @return mixed
         */
        public function respondBadRequest($message = 'Bad request!', $errors = null)
        {
            return $this->setStatusCode(400)->respondWithError($message, $errors);
        }

        /**
         * Generates a 403 json encoded error message
         *
         * @param string $message
         * @return mixed
         */
        public function respondForbidden($message = 'Forbidden!')
        {
            return $this->setStatusCode(403)->respondWithError($message);
        }

        /**
         * Generates a 404 json encoded error message
         *
         * @param $message
         * @return mixed
         */
        public function respondNotFound($message = 'Not found!')
        {

            return $this->setStatusCode(404)->respondWithError($message);
        }

        /**
         * Generates a 500 json encoded error message
         *
         * @param string $message
         * @return mixed
         */
        public function respondInternalError($message = 'Internal error!')
        {
            return $this->setStatusCode(500)->respondWithError($message);
        }


        /**
         * Generates a json response
         *
         * @param $data
         * @param array $headers
         * @return mixed
         */
        public function respond($data, $headers = [])
        {
            return response()->json($data, $this->getStatusCode());
        }

        /**
         * Creates an array with and error messages and a status code
         *
         * @param $message
         * @param null $errors
         * @return mixed
         */
        public function respondWithError($message, $errors = null)
        {
            return $this->respond([
                'response' => [
                    'message' => (isset($message['message']) ? $message['message'] : $message),
                    'created_id' => (isset($message['created_id']) ? $message['created_id'] : null),
                    'errors' => $errors,
                    'status_code' => $this->getStatusCode()
                ]
            ]);
        }

        /**
         * Helps us paginating the content
         *
         * @param $items
         * @param $data
         * @return mixed
         */
        public function respondWithPagination($items, $data)
        {
            $totalRecords = $items->total();

            $data = array_merge($data, [
                'paginator' => [
                    'total_count' => $totalRecords,
                    'total_pages' => ceil($totalRecords / $items->perPage()),
                    'current_page' => $items->currentPage(),
                    'limit' => $items->perPage(),
                ]
            ]);

            return $this->respond($data);
        }

    }
