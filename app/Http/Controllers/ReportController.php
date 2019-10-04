<?php

namespace App\Http\Controllers;

use App\ProfileReport;
use App\PostReport;
use App\User;
use App\Events\PostReported;
use App\Events\ProfileReported;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;

class ReportController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * TODO: Add comment report
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
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
                if ($return === false) {
                    $report = ProfileReport::create([
                        'author' => auth()->user()->id,
                        'reported_user' => $id
                    ]);
                    event(new ProfileReported($report));
                    return response()->json($report);
                }
                break;

            case 'post.report':
                // Check if item recently blocked
                $return = $this->wasBlockedRecently('profile', $id);

                // If not recently blocked create report, else return error
                if ($return === false) {
                    $report = PostReport::create([
                        'author' => auth()->user()->id,
                        'reported_post' => $id
                    ]);
                    event(new PostReported($report));
                    return response()->json($report);
                }
        }
        return response()->json($return);
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
                    ->whereDate(
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
                    ->whereDate(
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
