<?php

namespace App\Listeners;

use App\User;
use App\Photo;
use Illuminate\Http\File;
use App\Events\TransferImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImageDO implements ShouldQueue
{
    protected $finalPaths = [];
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
     * @param  TransferImage  $event
     * @return void
     */
    public function handle(TransferImage $event)
    {
        $user = $this->findUser($event->staffId);
        $this->uploadToDigitalOcean($event->imageArray, $user);
        $this->removePhotos($event->imageArray);
    }

    private function findUser($id){
        return User::findOrFail($id);
    }

    private function uploadToDigitalOcean($imageArray, $user){
        $digitalOceanFileStorageURL = "uploads/staff-photos/" . $user->slug . "/";
        $digitalOceanFinalFileStorageURL = "/uploads/staff-photos/" . $user->slug . "/";
        collect($imageArray)->each(function($item, $key) use ($user, $digitalOceanFileStorageURL, $digitalOceanFinalFileStorageURL){
            if($key !== 'original'){
                $photoName = explode('/',$item)[1];            
                \Storage::disk('spaces')->putFileAs($digitalOceanFileStorageURL, new File(storage_path() . "/app/" . $item), $photoName, 'public');
                $this->finalPaths[$key] = $digitalOceanFinalFileStorageURL . $photoName;
                // $this->finalPaths[$key] = '/uploads/staff-photos/' . $photoName;
            }else{
                \Storage::disk('spaces')->putFileAs($digitalOceanFileStorageURL, new File(storage_path() . "/app/upload/" . $item), $item);                
                $this->finalPaths[$key] = $digitalOceanFinalFileStorageURL . $item;            
                // $this->finalPaths[$key] = '/uploads/staff-photos/' . $item;            
            }
        });

        // dd($this->finalPaths);
        $this->savePhoto($user, $imageArray);
    }

    private function savePhoto($user, $imageArray){
        $photo = new Photo([
            'preferred' => false,
            'image_name' => substr($imageArray['small'], 7, -28),
            'image_thumb' => $this->finalPaths['thumb'],
            'image_sm' => $this->finalPaths['small'],
            'image_md' => $this->finalPaths['medium'],
            'image_lg' => $this->finalPaths['large'],
            'image_org' => $this->finalPaths['original'],
        ]);

        $user->photos()->save($photo);
    }

    private function removePhotos($imageArray){
        collect($imageArray)->each(function($item, $key){
            if($key !== 'original'){
                \Storage::delete($item);
            }else{
                \Storage::delete('upload/' . $item);
            }
        });
    }
}
