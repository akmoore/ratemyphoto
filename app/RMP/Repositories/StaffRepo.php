<?php

namespace App\RMP\Repositories;

use Storage;
use App\User;
use App\RMP\Interfaces\Staff as StaffInterface;

class StaffRepo implements StaffInterface{

    public function getRecords(){
        return User::where('role', 'staff')->with('photos', 'logins')->orderBy('slug')->get();
    }

    public function getRecord($slug){
        $user = User::with('photos','logins')->where('slug', $slug)->first();
        return $user;
    }

    public function storeRecord($request){
        $name = explode(' ', $request->name);
        $staff = User::create([
            'first_name' => $name[0],
            'last_name' => $name[1],
            'email' => $request->email
        ]);
        return $staff;
    }

    public function updateRecord($request, $id){
        $user = User::where('slug', $id)->firstOrFail();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->save();

        return $user;
    }

    public function deleteRecord($id){
        // return ['deleted' => $id];
        $user = User::findOrFail($id);

        //Delete All Assoc Photos from directory
        $directory = "/uploads/staff-photos/" . $user->slug . "/";
        $storage = \Storage::disk('spaces')->deleteDirectory($directory);  

        // return ['directory' => $directory, 'storage' => $storage];        

        //Delete User
        $user->delete();

        //Return the deleted user
        return $user;
    }

}