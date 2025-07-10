<?php

namespace App\Providers;

use App\Listeners\ClearUserAllLessonsCache;
use App\Listeners\ClearUserAllLessonSlotsCache;
use App\Listeners\ClearUserDateLessonsCache;
use App\Listeners\ClearUserLessonTimesCache;
use App\Listeners\UpdateLessonTimeLessons;
use App\Listeners\UpdateStudentLessons;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [

        //      LessonTime
        'eloquent.created: App\Models\LessonTime' => [
            ClearUserAllLessonSlotsCache::class,
            ClearUserAllLessonsCache::class,
            ClearUserLessonTimesCache::class,
        ],
        'eloquent.deleted: App\Models\LessonTime' => [
            ClearUserAllLessonSlotsCache::class,
            ClearUserAllLessonsCache::class,
            ClearUserLessonTimesCache::class,
        ],
        'eloquent.updated: App\Models\LessonTime' => [
            ClearUserAllLessonSlotsCache::class,
            ClearUserAllLessonsCache::class,
            UpdateLessonTimeLessons::class,
            ClearUserLessonTimesCache::class,
        ],

        //      Student
        'eloquent.deleted: App\Models\Student' => [
            ClearUserAllLessonsCache::class,
        ],
        'eloquent.updated: App\Models\Student' => [
            ClearUserAllLessonsCache::class,
            UpdateStudentLessons::class,
        ],

        //      FreeTime
        'eloquent.created: App\Models\FreeTime' => [
            ClearUserAllLessonSlotsCache::class,
        ],
        'eloquent.deleted: App\Models\FreeTime' => [
            ClearUserAllLessonSlotsCache::class,
        ],
        'eloquent.updated: App\Models\FreeTime' => [
            ClearUserAllLessonSlotsCache::class,
        ],

        //      Lesson
        'eloquent.created: App\Models\Lesson' => [
            ClearUserDateLessonsCache::class,
        ],
        'eloquent.updated: App\Models\Lesson' => [
            ClearUserDateLessonsCache::class,
        ],

    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
