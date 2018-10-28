<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RMP\Interfaces\Staff;

class StaffController extends Controller
{
    protected $staff;

    public function __construct(Staff $staff)
    {
        $this->staff = $staff;
        $this->middleware('auth:api');
    }

    /**
     * Return staff except admin
     *
     * @return void
     */
    public function index(){
        if(auth()->user()->role !== 'admin'){
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $this->staff->getRecords();
    }

    /**
     * Adding Staff
     *
     * @param Request $request
     * @return object
     */
    public function store(Request $request){
        if(auth()->user()->role !== 'admin'){
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $this->staff->storeRecord($request);
    }

    public function show($slug){
        if(auth()->user()->role !== 'admin'){
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $this->staff->getRecord($slug);
    }

    public function update(Request $request, $slug){
        if(auth()->user()->role !== 'admin'){
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // return ['slug' => $slug, 'request' => $request->all()];
        return $this->staff->updateRecord($request, $slug);
    }

    public function delete($id){
        if(auth()->user()->role !== 'admin'){
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $this->staff->deleteRecord($id);
    }
}
