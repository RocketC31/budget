<?php

namespace App\Http\Controllers;

use App\Models\Widget;
use App\Widgets\BalanceGlobal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//TODO : not make widget so abstract. Go to make dedicated component

class WidgetController extends Controller
{
    public function refresh(Widget $widget): RedirectResponse
    {
        if (config("app.redis_available") && $widget->type === "balance_global") {
            $widget = $widget->resolve();
            $widget->refreshRedisBalance();
        }

        return back();
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => 'string|in:' . implode(',', array_keys(config('widgets.types'))),
        ]);

        $user = Auth::user();

        $existingWidgetsAmount = count($user->widgets());

        Widget::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'sorting_index' => $existingWidgetsAmount,
            'properties' => $request->providedProperties
        ]);

        return back();
    }

    public function up(Widget $widget): RedirectResponse
    {
        if ($widget->sorting_index > 0) {
            $previousWidget = Widget::where('user_id', $widget->user_id)
                ->where('sorting_index', $widget->sorting_index - 1)
                ->first();

            $previousWidget->update([
                'sorting_index' => $widget->sorting_index
            ]);

            $widget->update([
                'sorting_index' => $widget->sorting_index - 1
            ]);
        }

        return back();
    }

    public function down(Widget $widget): RedirectResponse
    {
        $widgets = Auth::user()->widgets();
        if ($widget->sorting_index < count($widgets) - 1) {
            $nextWidget = Widget::where('user_id', $widget->user_id)
                ->where('sorting_index', $widget->sorting_index + 1)
                ->first();

            $nextWidget->update([
                'sorting_index' => $widget->sorting_index
            ]);

            $widget->update([
                'sorting_index' => $widget->sorting_index + 1
            ]);
        }

        return back();
    }

    public function delete(Widget $widget): RedirectResponse
    {
        $widgetResolve = $widget->resolve();
        $widget->delete();

        if ($widgetResolve instanceof BalanceGlobal) {
            $widgetResolve->removeRedisKeys();
        }

        /**
         * Move widgets with out-of-sync sorting index up by 1
         */

        $outOfSyncWidgets = Widget::where('user_id', $widget->user_id)
            ->whereRaw('sorting_index > ?', [$widget->sorting_index])
            ->get();

        foreach ($outOfSyncWidgets as $outOfSyncWidget) {
            $outOfSyncWidget->update([
                'sorting_index' => $outOfSyncWidget->sorting_index - 1
            ]);
        }

        return back();
    }
}
