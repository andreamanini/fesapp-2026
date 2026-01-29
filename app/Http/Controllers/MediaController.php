<?php

    namespace App\Http\Controllers;

    use App\Media;
    use Facade\FlareClient\Http\Exceptions\NotFound;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Str;
    use Intervention\Image\Exception\NotFoundException;
    use Intervention\Image\Facades\Image;
    /*
    use lsolesen\pel\PelEntryAscii;
    use lsolesen\pel\PelEntryByte;
    use lsolesen\pel\PelEntryRational;
    use lsolesen\pel\PelEntryUserComment;
    use lsolesen\pel\PelExif;
    use lsolesen\pel\PelIfd;
    use lsolesen\pel\PelJpeg;
    use lsolesen\pel\PelTag;
    use lsolesen\pel\PelTiff;
     * 
     */

    class MediaController extends ApiController
    {

        /**
         * Store a newly created resource in storage.
         *
         * @param  \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            // File mime type validation
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:'.env('UPLOAD_ALLOWED_IMAGE_TYPES').','.env('UPLOAD_ALLOWED_FILE_TYPES'),
                'mediable_id' => 'required',
                'mediable_type' => 'required|in:App\BuildingSite,App\Machinery',
            ]);

            if ($validator->fails()) {
                return $this->respondBadRequest('Some parameters are wrong or missing', $validator->errors());
            }

            // Sluggify the filename
            $fileType = 'image';
            $fileExt = strtolower($request->file('file')->getClientOriginalExtension());
            $fileName = $this->sluggifyFileName($request->file('file')->getClientOriginalName(), $fileExt);

            // Based on the mediable_type we can define which directory the media file should be saved into
            switch($request->input('mediable_type')) {
                case 'App\BuildingSite':
                    $directory = 'building-site-'.$request->input('mediable_id');
                    break;
                case 'App\Machinery':
                    $directory = 'machine-'.$request->input('mediable_id');
                    break;
                default:
                    $directory = date('YmdHis');
            }

            try {
                // Get the uploaded image contents
                $fileContents = file_get_contents($request->file('file'));

                // Check if the file already exists, if so add an extension to the name
                if (is_file($directory.'/'.$fileName.'.'.$fileExt)) {
                    $fileName .= '-'.date('YmdHis');
                }

                // If the file is an image... or if it's a file...there is a different storing method
                if (substr($request->file('file')->getMimeType(), 0, 5) == 'image') {
                    $imageSizes = $this->processImages($fileContents, $directory, $fileName, $fileExt);
                }


                // Store the uploaded file in the public media directory

                // Add the file to the media table
                $media = new Media();

                $media->media_name = $fileName;

                $media->extension = $fileExt;

                $media->directory = $directory;

                $media->media_type = $fileType;

                $media->mediable_id = $request->input('mediable_id');

                $media->mediable_type = $request->input('mediable_type');

                $media->note_id = $request->input('note_id');

                if (null !== $request->input('lat') and null !== $request->input('lng')) {
                    $media->coordinates = json_encode([
                        'lat' => $request->input('lat'),
                        'lng' => $request->input('lng'),
                    ]);
                }

                $media->job_proof = (1 == $request->input('job_proof') ? 1 : 0);

                $media->created_by = auth()->user()->name . ' ' . auth()->user()->surname;

                $media->save();

            } catch (\Exception $e) {
                return $this->respondInternalError('An error has occurred while trying upload this file.' . $e->getMessage());
            }

            return $this->respondCreated();
        }

        /**
         * Download the document file
         *
         * @param Media $medium
         * @return mixed
         */
        public function downloadFile(Media $medium)
        {
            // TODO: should we check for any type of file permission before allowing the user to download / view the file ?
            try {
                if (Storage::disk('documents')->exists("{$medium->directory}/{$medium->media_name}.{$medium->extension}")) {
                    return Storage::download("documents/{$medium->directory}/{$medium->media_name}.{$medium->extension}");
                } else {
                    abort(404);
                }
            } catch (\Exception $e) {
                Log::error('Errore durante il download di un file: '. $e->getMessage());
                abort(500, $e->getMessage());
            }
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param Media $medium
         * @return mixed
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function destroy(Media $medium)
        {
            if ($this->authorize('delete', $medium)) {

                // Try to delete the media record, files and relationships
                try {
                    // Delete the media file from the directory
                    if (is_dir(public_path("media/{$medium->directory}"))) {

                        $mediaDir = scandir(public_path("media/{$medium->directory}"));
                        $deleteArray = array_map(function ($v) use ($medium) {
                            return "{$v}{$medium->media_name}.{$medium->extension}";
                        }, $medium->thumbNames);

                        foreach ($mediaDir as $file) {
                            if ($file == "{$medium->media_name}.{$medium->extension}" or in_array($file, $deleteArray)) {
                                File::delete(public_path("media/{$medium->directory}/{$file}"));
                            }
                        }

                        // If the directory is empty, delete it entirely
                        if (count(scandir(public_path("media/{$medium->directory}"))) == 2) {
                            File::deleteDirectory(public_path("media/{$medium->directory}"));
                        }
                    }

                    // Delete the record in the db
                    $medium->delete();

                } catch (\Exception $e) {
                    return $this->respondInternalError('An error has occurred while trying to delete the media file.'. $e->getMessage());
                }

                return $this->respondCreated('The media has been deleted successfully.');
            }
        }

        /**
         * Opens the image tagging window
         *
         * @param Media $media
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function imageTagging(Media $media)
        {
            if ($this->authorize('tagImage', $media)) {
                return view('backend.media.image-tagging', compact('media'));
            }
        }

        /**
         * Stores the media image tags as JSON column on the media image record
         *
         * @param Request $request
         * @param Media $media
         * @return mixed
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function storeImageTags(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'media_id' => 'required|exists:media,id',
                'pins' => 'nullable|array',
            ]);

            // Validate the request
            if ($validator->fails()) {
                return $this->respondBadRequest($validator->errors());
            }

            // Get the media record
            $media = Media::find($request->input('media_id'));

            // Check for authorizations
            if ($this->authorize('tagImage', $media)) {

                try {

                    $media->notes = (null !== $request->input('pins') ? json_encode($request->input('pins')) : null);

                    $media->save();

                } catch (\Exception $e) {
                    return $this->respondInternalError('An error has occurred while trying to save the media tag.');
                }

                return $this->respondCreated('Media image tagged successfully.');
            }
        }

        /**
         * Function used to update the image sorting when dragged and dropped within the edit product admin page
         *
         * @param Request $request
         * @return mixed
         */
        public function mediaImageOrdering(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'media_ids' => 'required|array'
            ]);

            if ($validator->fails()) {
                return $this->respondBadRequest('Some parameters are wrong or missing', $validator->errors());
            }

            try {
                for($c=0; $c<count($request->input('media_ids')); $c++) {
                    Media::where('id', '=', $request->input('media_ids')[$c])
                        ->update([
                            'primary' => ($c==0 ? 1 : 0),
                            'ordering' => $c,
                        ]);
                }
            } catch (\Exception $e) {
                return $this->respondInternalError('An error has occurred while trying to update the image ordering. '.$e->getMessage());
            }

            return $this->respondCreated('Media ordering updated successfully');
        }

        /**
         * Function used to upload media files via dropzone js
         *
         * @param Request $request
         * @return mixed
         */
        public function mediaFileUpload(Request $request)
        {
            $request->validate([
                'file' => 'required|mimes:'.env('UPLOAD_ALLOWED_IMAGE_TYPES').','.env('UPLOAD_ALLOWED_FILE_TYPES'),
                'building_site_id' => 'required|exists:building_sites,id'
            ]);

            try {
                // Compose a directory name based on the building site
                $directory = "documents/building-site-{$request->input('building_site_id')}";

                // Check if the directory exists, otherwise create it
                if (!File::exists(storage_path($directory))) {
                    File::makeDirectory(storage_path($directory), 0775, true);
                }

                // Process the filename
                $documentExt = strtolower($request->file('file')->getClientOriginalExtension());
                $documentName = sluggifyFileName($request->file('file')->getClientOriginalName(), $documentExt);

                // Store the attachment
                $attachment = $request->file('file')->storeAs(
                    $directory,
                    $documentName. '.' . $documentExt
                );

                // Create a row in the media table to link the building site and the document
                $media = Media::create([
                    'media_name' => $documentName,
                    'extension' => $documentExt,
                    'directory' => "building-site-{$request->input('building_site_id')}",
                    'media_type' => 'file',
                    'mediable_id' => $request->input('building_site_id'),
                    'mediable_type' => 'App\BuildingSite',
                    'created_by' => auth()->user()->name . ' ' . auth()->user()->surname
                ]);

            } catch (\Exception $e) {
                return $this->respondInternalError('An error has occurred while trying to upload the file. '.$e->getMessage());
            }

            return $this->respondCreated('Media file has been uploaded successfully.');
        }

        /**
         * Process image function
         *
         * @param string $imageContents
         * @param string $directory
         * @param string $fileName
         * @param string $fileExt
         * @return object
         */
        private function processImages(string $imageContents, string $directory, string $fileName, string $fileExt)
        {
            // Use intervention image library to process the uploaded image
            //$image = Image::make($imageContents);
            $image = Image::make($_FILES['file']['tmp_name'])->orientate();

            // Check if the directory for the images exists, or create it
            if (!File::exists("media/$directory")) {
                File::makeDirectory("media/$directory", 0775, true);
            }

            // Working on image dimensions --> keep proportions or crop
            if (true === env('KEEP_IMG_PROPORTIONS')) {
                $imageMaxWidth = ($image->width() >= $image->height() ? env('IMG_MAX_WIDTH') : null);
                $imageMaxHeight = ($image->width() <= $image->height() ? env('IMG_MAX_HEIGHT') : null);

                // Resize the image to the chosen dimentions while keeping proportions
                $image->resize($imageMaxWidth, $imageMaxHeight, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save("media/$directory/$fileName.$fileExt", 90);

            } else {
                $imageMaxWidth = ($image->width() > env('IMG_MAX_WIDTH') ? env('IMG_MAX_WIDTH') : $image->width());
                $imageMaxHeight = ($image->height() > env('IMG_MAX_HEIGHT') ? env('IMG_MAX_HEIGHT') : $image->height());

                // Fit the image to the chosen dimentions without keeping proportions
                $image->fit($imageMaxWidth, $imageMaxHeight, function($constraint) {
                    $constraint->upsize();
                })->save("media/$directory/$fileName.$fileExt", 90);

            }
            /*
            // copy exif data
            $inputPel = new PelJpeg($_FILES['file']['tmp_name']);
            $outputPel = new PelJpeg("media/$directory/$fileName.$fileExt");
            if ($exif = $inputPel->getExif()) {
                $outputPel->setExif($exif);
                $outputPel->saveFile();
            }*/
            // Get the image width and height of the main image
            $mainImageWidth = $image->width();
            $mainImageHeight = $image->height();


            // Check if we should keep proportions for the main thumbnail
            if (env('KEEP_THUMB_PROPORTIONS')) {
                $thumbMaxWidth = ($image->width() >= $image->height() ? env('THUMB_MAX_WIDTH') : null);
                $thumbMaxHeight = ($image->width() <= $image->height() ? env('THUMB_MAX_HEIGHT') : null);
            } else {
                $thumbMaxWidth = env('THUMB_MAX_WIDTH');
                $thumbMaxHeight = env('THUMB_MAX_HEIGHT');
            }

            // Create the thumbnail
            $image->fit($thumbMaxWidth, $thumbMaxHeight, function($constraint) {
                $constraint->upsize();
            })->save("media/$directory/thumb_$fileName.$fileExt", 90);


            return (object)[
                'width' => $mainImageWidth,
                'height' => $mainImageHeight
            ];
        }

        /**
         * Function used to sluggify file names
         *
         * @param string $fileName
         * @param string $ext
         * @return mixed|string
         */
        private function sluggifyFileName(string $fileName, string $ext)
        {
            $fileName = strtolower(Str::slug($fileName, '-'));
            $fileExt = strtolower($ext);
            $fileName = str_replace($fileExt, '', $fileName);

            return $fileName;
        }

    }
