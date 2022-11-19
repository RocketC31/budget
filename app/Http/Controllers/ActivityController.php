<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $activities = Activity::ofSpace(session('space_id'))->orderBy('created_at', 'DESC')->paginate(20);

        return Inertia::Render('Activities/Index', ['activities' => $activities]);
    }
}
