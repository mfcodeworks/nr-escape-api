<?php

namespace App\Jobs;

use App\PostReport;
use App\ProfileReport;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminReportSummary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get recent unchecked reports
     * Order by reports then date
     *
     * Profiles: SELECT
     *     reported_user as profile,
     *     count(*) as reports,
     *     (SELECT posts.updated_at FROM posts WHERE posts.id = post_reports.reported_post) as post_date
     * FROM `profile_reports`
     * WHERE checked = 0
     * GROUP BY reported_user
     * ORDER BY reports, created_at
     *
     * Posts: SELECT
     *     post_reports.reported_post as post,
     *     count(*) as reports,
     *     (
     *         SELECT posts.updated_at
     *         FROM posts
     *         WHERE posts.id = post_reports.reported_post
     *     ) as post_date
     * FROM post_reports
     * WHERE post_reports.checked = 0
     * OR (
     *         SELECT posts.updated_at
     *         FROM posts
     *         WHERE posts.id = post_reports.reported_post
     * ) < post_reports.checked_at
     * GROUP BY post_reports.reported_post
     * ORDER BY reports, post_date
     *
     * @return void
     */
    public function handle()
    {
        $profiles = DB::table('profile_reports')
            ->select(
                'reported_user as profile',
                DB::Raw('count(*) as reports'),
            )
            ->where('checked', 0)
            ->groupBy('reported_user')
            ->orderBy('reports', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        Log::notice($profiles);

        $posts = DB::table('post_reports')
            ->select(
                'post_reports.reported_post as post',
                DB::Raw('count(*) as reports'),
                DB::Raw('(SELECT posts.updated_at FROM posts WHERE posts.id = post_reports.reported_post) as post_date')
            )
            ->where('checked', 0)
            ->orWhere(
                DB::Raw('(SELECT posts.updated_at FROM posts WHERE posts.id = post_reports.reported_post)'),
                '>',
                'checked_at'
            )
            ->groupBy('reported_post')
            ->orderBy('reports', 'desc')
            ->orderBy('post_date', 'desc')
            ->get();
        Log::notice($posts);

        // Email out the reports
        $beautymail = app()->make('Snowfire\Beautymail\Beautymail');
        $beautymail->send('emails.admin.reports', [
            'posts' => $posts,
            'profiles' => $profiles
        ], function($message) {
            $message
                ->from('it@nygmarosebeauty.com')
                ->to('it@nygmarosebeauty.com', 'NR IT')
                ->subject('[Reports] NR Escape Reposts Summary');
        });
    }
}
