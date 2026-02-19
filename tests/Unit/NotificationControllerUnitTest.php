<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\NotificationController;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessNotification;

class NotificationControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_creates_notification_and_dispatches_job()
    {
        Queue::fake();

        $user = User::factory()->create();

        $controller = new NotificationController();

        $request = Request::create('/notifications', 'POST', [
            'user_id' => $user->id,
            'type' => 'email',
            'message' => 'Unit Test Message'
        ]);

        $response = $controller->store($request);

        $this->assertEquals(201, $response->status());

        $this->assertDatabaseHas('notifications', [
            'message' => 'Unit Test Message'
        ]);

        Queue::assertPushed(ProcessNotification::class);
    }

    /** @test */
    public function recent_returns_cached_notifications()
    {
        Cache::flush(); 

        Notification::factory()->count(3)->create();
    
        $controller = new NotificationController();
    
        $result = $controller->recent();
    
        $this->assertCount(3, $result);
    }

    /** @test */
    public function summary_returns_correct_counts()
    {
        Cache::flush(); 

        Notification::factory()->create(['status' => 'sent']);
        Notification::factory()->create(['status' => 'failed']);
    
        $controller = new NotificationController();
    
        $result = $controller->summary();
    
        $this->assertEquals(1, $result['sent']);
        $this->assertEquals(1, $result['failed']);
    }
}
