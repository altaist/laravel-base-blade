<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function store(FeedbackRequest $request)
    {
        $validated = $request->validated();
        
        Feedback::create([
            'json_data' => $validated
        ]);
        
        return back()->with('success', 'Спасибо за обратную связь!');
    }

    /**
     * Показать список всех фидбеков (только для админа)
     */
    public function index(): View
    {
        $feedbacks = Feedback::orderBy('created_at', 'desc')->paginate(20);
        
        return view('pages.admin.feedbacks.index', compact('feedbacks'));
    }

    /**
     * Показать конкретный фидбек (только для админа)
     */
    public function show(Feedback $feedback): View
    {
        return view('pages.admin.feedbacks.show', compact('feedback'));
    }
}
