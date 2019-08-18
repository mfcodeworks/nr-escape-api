<?php

namespace App\Http\Controllers;

use App\ProfileReport;
use App\PostReport;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;

class ReportController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * // TODO: Alert staff if user reported high number of times
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        // Check if report is profile or post
        switch ($request->route()->getName()) {
            case 'profile.report':
                // Check if item recently blocked
                $return = $this->wasBlockedRecently('profile', $id);

                // If not recently blocked create report, else return error
                return ($return === false)
                ? ProfileReport::create([
                    'author' => auth()->user()->id,
                    'reported_user' => $id
                ])
                : $return;
                break;

            case 'post.report':
                // Check if item recently blocked
                $return = $this->wasBlockedRecently('profile', $id);

                // If not recently blocked create report, else return error
                return ($return === false)
                ? PostReport::create([
                    'author' => auth()->user()->id,
                    'reported_post' => $id
                ])
                : $return;
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

    /**
     * Check if existing block within past day
     *
     * @param string $type
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function wasBlockedRecently($type, $id) {
        // For the report type, check if recent block exists
        switch ($type) {
            case 'post':
                $reported = auth()->user()
                    ->profileReports
                    ->where('reported_user', $id)
                    ->where(
                        'created_at',
                        '>',
                        Carbon::now()->subDay()->toDateTimeString()
                    )->first()
                ? true : false;
                break;

            case 'profile':
                $reported = auth()->user()
                    ->postReports
                    ->where('reported_post', $id)
                    ->where(
                        'created_at',
                        '>',
                        Carbon::now()->subDay()->toDateTimeString()
                    )->first()
                ? true : false;
                break;
        }

        // If a report was found in the last 24 hours return error, else return false
        return $reported ? response()->json([
            'error' => "$type already reported, please wait 1 day between reporting $type"
        ], 400) : false;
    }
}
