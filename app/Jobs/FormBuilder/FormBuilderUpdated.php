<?php

namespace App\Jobs\FormBuilder;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Skillz\Nnpcreusable\Service\FormService;
use Skillz\Nnpcreusable\Service\NotificationTaskService;

class FormBuilderUpdated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $data;
    private int $id;
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->id = $data["id"];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new FormService();
        $data = $this->data;
        $service->updateForm($data, $this->id);
    }
}
