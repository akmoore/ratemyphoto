<?php

namespace App\Http\Controllers;

use Storage;
use App\User;
use App\Photo;
use App\Download;
use Illuminate\Http\Request;
use App\Events\ImageUploaded;
use App\RMP\Interfaces\Photo as PI;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;

class PhotoController extends Controller
{
    protected $photo;

    public function __construct(PI $photo)
    {
        $this->photo = $photo;
        $this->middleware('auth:api', ['except' => ['download']]);
    }

    public function index(Request $request){
        // return $this->photo->getRecords();
        $staff = User::with('photos')->find($request->staff);
        // $photos = $staff->photos()->whereFeatured(true)->get();
        $featured = $staff->photos->whereIn('featured', true);
        if(count($featured)){
            return [$staff->photos()->whereFeatured(true)->get(), true];
        }else{
            return [$staff->photos()->get(), false];
        }
        // return [$staff->photos, false];
        // return $photos;
    }

    public function prefer($id){
        $preferredPhoto = Photo::findOrFail($id);
        $currentPreferred = auth()->user()->photos->where('preferred', 1)->first();

        if(!auth()->user()->can_select){
            return response()->json(['error' => "Selecting is disabled."], 401);
        }
        
        if($currentPreferred){
            $currentPreferred->preferred = 0;
            $currentPreferred->save();
        }
        
        $preferredPhoto->preferred = 1;
        $preferredPhoto->save();

        auth()->user()->has_selected_preferred_image = \Carbon\Carbon::now();
        auth()->user()->save();
        
        return auth()->user()->id;
    }

    public function download($id){
        $photo = Photo::findOrFail($id);
        $download = new Download();
        $photo->downloads()->save($download);

        $headers = array(
                  'Content-Type: image/jpeg',
                );

        // return \Storage::disk('spaces')->download('uploads/staff-photos/sales-connect/IMG_2958_79985faef5d43ff00291286af11c0b30.JPG', 'filename.jpg', $headers);
        return \Storage::disk('spaces')->temporaryUrl(ltrim($photo->image_org, '/'), now()->addMinutes(5));
    }

    // public function show($id){
    //     return $this->photo->getRecord($id);
    // }

    public function store(Request $request){
        if(auth()->user()->role !== 'admin'){
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        // receive the file
        $save = $receiver->receive();
        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            $this->saveFile($save->getFile(), $request);
            return 'file is saving';
        }
        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();
        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    public function delete($id){
        if(auth()->user()->role !== 'admin'){
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $photo = Photo::findOrFail($id);
        $user = User::findOrFail($photo->user_id);
        $directory = "/uploads/staff-photos/" . $user->slug . "/";        
        $photoArray = [
            $photo->image_thumb,
            $photo->image_sm,
            $photo->image_md,
            $photo->image_lg,
            $photo->image_org
        ];

        collect($photoArray)->each(function($photo){
            \Storage::disk('spaces')->delete($photo);
        });

        $photo->delete();

        return $photo;

    }

    /**
     * Saves the file
     *
     * @param UploadedFile $file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function saveFile(UploadedFile $file, Request $staff)
    {
        $fileName = $this->createFilename($file);
        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());
        // Group files by the date (week
        $dateFolder = date("Y-m-W");
        // Build the file path
        // $filePath = "upload/{$mime}/{$dateFolder}/";
        $filePath = "upload/";
        $finalPath = storage_path("app/".$filePath);
        // move the file name
        $file->move($finalPath, $fileName);

        // Execute an event queue, passing in the $finalPath and $staff
        event(new ImageUploaded("app/upload/", $fileName, $file->getClientOriginalName(), $staff->staff));

        // return response()->json([
        //     'path' => $filePath,
        //     'name' => $fileName,
        //     'mime_type' => $mime
        // ]);

    }
    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension
        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;
        return $filename;
    }
}
