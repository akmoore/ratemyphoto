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
        return $this->staff->getRecords();
    }

    /**
     * Adding Staff
     *
     * @param Request $request
     * @return object
     */
    public function store(Request $request){
        return $this->staff->storeRecord($request);
    }

    public function show($slug){
        // return $slug;
        return $this->staff->getRecord($slug);
    }

    public function update(Request $request, $slug){
        // return ['slug' => $slug, 'request' => $request->all()];
        return $this->staff->updateRecord($request, $slug);
    }

    public function delete($id){
        return $this->staff->deleteRecord($id);
    }
}
