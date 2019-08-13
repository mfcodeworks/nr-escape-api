<?php

namespace App\Http\Controllers;

use App\Report;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;

class ReportController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        // Validate block info
        $validator = Validator::make($request->all(), [
            'author' => 'required|exists:users,id',
            'reported_user' => 'required|exists:users,id'
        ]);
        if ($validator->fails() || auth()->user()->id !== $request->author || $request->reported_user !== intval($id)) {
            return response()->json([
                'error' => 'Unable to report user',
                'validator' => $validator->errors()
            ], 400);
        }

        // Check if existing block within past day
        if (auth()->user()
            ->reports
            ->where('reported_user', $id)
            ->where(
                'created_at',
                '>',
                Carbon::now()->subDay()->toDateTimeString()
            )
            ->first()
        ) {
            return response()->json([
                'error' => 'User already reported, please wait 1 day between reporting a user'
            ], 400);
        }

        // Create report
        $report = Report::create([
            'reported_user' => $request->reported_user,
            'author' => $request->author
        ]);

        // Return report creation response
        if ($report) {
            return response()->json($report, 201);
        } else {
            return response()->json([
                'error' => 'Failed to report user'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        // Select report by ID
        return Report::find($id);
    }
}
