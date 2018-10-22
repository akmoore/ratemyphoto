<?php

namespace App\RMP\Interfaces;

interface _Main {
    public function getRecords();
    public function getRecord($id);
    public function storeRecord($request);
    public function updateRecord($request, $id);
    public function deleteRecord($id);
}