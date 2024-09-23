<?php

namespace App\Jobs\FormData;

use Illuminate\Bus\Queueable;
use Skillz\Nnpcreusable\Models\Tag;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Skillz\Nnpcreusable\Service\FormService;

class FormDataCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private array $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // this would be used by other services to determine how to handle the data that comes 
        // get all tags 
        $tagModel = Tag::all();
        $fetschedData = $this->data['tag']["name"];
        foreach ($tagModel as $tag) {
            if ($tag->name == $fetschedData) {
                $operationClass = new $tag->tag_class();
                $operationClass->{$tag->tag_class_method}($this->data);
            }
        }

        //pull the data from the queue data and point it the class that would handle it 
        //use incoming class and method from queue and also pass the data thats coming in
        // this section can be ignored if you want or commented out if you want

        $service = new FormService();
        $data = $this->data;
        $service->createFormData($data);
    }
}
