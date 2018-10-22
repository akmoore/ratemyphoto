<?php

namespace App\Listeners;

use Illuminate\Http\File;
use App\Events\ImageUploaded;
use App\Events\TransferImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConvertImage implements ShouldQueue
{
    // protected $file;
    protected $sizes = [];

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ImageUploaded  $event
     * @return void
     */
    public function handle(ImageUploaded $event)
    {  

        $path = storage_path($event->path . $event->file_name);
        
        $this->sizes['original'] = $event->file_name;
        $this->convertThumb($event, $path);
        $this->convertSmall($event, $path);
        $this->convertMedium($event, $path);
        $this->convertLarge($event, $path);

        event(new TransferImage($this->sizes, $event->staff_id));
    }

    public function convertThumb($event, $path){
        $img = \Image::make($path)->orientate()->fit(200, 200, function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
        }, 'top');
        $name = $img->basename;
        $original_name = substr($event->original_file_name, 0, -4) . '_thumb_' . str_random(20) . '.jpg';
        $currentPath = "photos/{$original_name}";
        \Storage::put($currentPath, (string) $img->encode('jpg', 100));
        $this->sizes['thumb'] = $currentPath;
    }

    public function convertSmall($event, $path){
        $img = \Image::make($path)->orientate()->fit(700, 700, function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
        }, 'top');
        $name = $img->basename;
        $original_name = substr($event->original_file_name, 0, -4) . '_sm_' . str_random(20)  . '.jpg';
        $currentPath = "photos/{$original_name}";
        \Storage::put($currentPath, (string) $img->encode('jpg', 80));
        $this->sizes['small'] = $currentPath;
    }

    public function convertMedium($event, $path){
        $img = \Image::make($path)->resize(1280, null, function($constraint){
            $constraint->aspectRatio();
        })->orientate();
        $name = $img->basename;
        $original_name = substr($event->original_file_name, 0, -4) . '_md_' . str_random(20)  . '.jpg';
        $currentPath = "photos/{$original_name}";
        \Storage::put($currentPath, (string) $img->encode('jpg', 70));
        $this->sizes['medium'] = $currentPath;
    }

    public function convertLarge($event, $path){
        $img = \Image::make($path)->resize(1920, null, function($constraint){
            $constraint->aspectRatio();
        })->orientate();
        $name = $img->basename;
        $original_name = substr($event->original_file_name, 0, -4) . '_lg_' . str_random(20)  . '.jpg';
        $currentPath = "photos/{$original_name}";
        \Storage::put($currentPath, (string) $img->encode('jpg', 70));
        $this->sizes['large'] = $currentPath;
    }
}
