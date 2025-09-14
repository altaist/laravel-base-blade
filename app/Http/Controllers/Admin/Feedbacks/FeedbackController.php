<?php

namespace App\Http\Controllers\Admin\Feedbacks;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{

    /**
     * Переопределяем метод index для feedbacks
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        
        if ($search) {
            $feedbacks = Feedback::where('json_data->name', 'like', "%{$search}%")
                ->orWhere('json_data->comment', 'like', "%{$search}%")
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $feedbacks = Feedback::orderBy('created_at', 'desc')->paginate(15);
        }

        return view('pages.admin.feedbacks.index', compact('feedbacks', 'search'));
    }

    /**
     * Переопределяем метод show для feedbacks
     */
    public function show(Feedback $feedback): View
    {
        return view('pages.admin.feedbacks.show', compact('feedback'));
    }

    /**
     * Feedbacks не поддерживают create, update, destroy
     */
    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function edit(Feedback $feedback)
    {
        abort(404);
    }

    public function update(Request $request, Feedback $feedback)
    {
        abort(404);
    }

    public function destroy(Feedback $feedback)
    {
        abort(404);
    }
}
