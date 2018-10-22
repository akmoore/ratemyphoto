<?php

namespace App\RMP\Repositories;

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
        // $staff->password = bcrypt($staff->slug);
        // $staff->save();
        return $staff;
    }

    public function updateRecord($request, $id){
        //
    }

    public function deleteRecord($id){
        //
    }

}