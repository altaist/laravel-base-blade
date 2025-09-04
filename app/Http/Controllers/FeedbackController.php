<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\Request;

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
}
