<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaingController extends Controller
{
    public function index()
    {

        $campings = Campaign::all();

        return view('campaings.index', compact('campings'));
    }

}
