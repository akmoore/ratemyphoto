<?php

namespace App\RMP\Repositories;

use Storage;
use App\User;
use App\RMP\Interfaces\Staff as StaffInterface;

class StaffRepo implements StaffInterface{

    public function getRecords(){
        return User::where('role', 'staff')->with('photos')->get();
    }

    public function getRecord($slug){
        $user = User::with('photos')->where('slug', $slug)->first();
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
        return $user;

        //Delete All Assoc Photos from directory
        $directory = "/uploads/staff-photos/" . $user->slug . "/";
        \Storage::disk('spaces')->deleteDirectory($directory);  

        //Delete User
        $user->delete();

        //Return the deleted user
        return $user;
    }

}