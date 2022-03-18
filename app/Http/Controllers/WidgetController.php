<?php

namespace App\Http\Controllers;

use App\Models\Widget;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function show(Request $request, Widget $widget)
    {
        $this->authorize('view', $widget);
        return $widget->render();
    }
}
