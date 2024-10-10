<?php

namespace App\Jobs\FormData;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Skillz\Nnpcreusable\Models\Tag;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Skillz\Nnpcreusable\Service\FormService;
use Skillz\Nnpcreusable\Service\NotificationTaskService;

class FormDataUpdated implements ShouldQueue
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
        $tagModel = Tag::all();
        $fetschedData = $this->data['tag']["name"];
        foreach ($tagModel as $tag) {
            if ($tag->name == $fetschedData) {
                $operationClass = new $tag->tag_class();
                $operationClass->{$tag->tag_class_method}($this->data);
            }
        }

        // this area can be commented out if you do not need to have the form data saved or updated on your service
        $service = new FormService();
        $data = $this->data;
        $service->updateFormData($data, $this->id);
    }
}
