<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::ofSpace(session('space_id'))->get();

        return Inertia::Render('Activities/Index', ['activities' => $activities]);
    }
}
