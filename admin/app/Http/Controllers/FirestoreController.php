<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;

class FirestoreController extends Controller
{
    public function clearUsers()
    {
        $firestore = new FirestoreService();
        return $firestore->clearCollection('rides');
    }

    // generic version (delete any collection)
    public function clearCollection($name)
    {
        $firestore = new FirestoreService();
        return $firestore->clearCollection($name);
    }
}
