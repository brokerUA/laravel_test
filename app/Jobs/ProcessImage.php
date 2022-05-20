<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use function Tinify\fromFile;
use function Tinify\setKey;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        setKey(config('app.tinify'));

        $source = fromFile(public_path('storage/' . $this->user->photo));

        $resized = $source->resize([
            "method" => "cover",
            "width" => 70,
            "height" => 70
        ]);

        $newFileName = str_replace('__raw__', '', $this->user->photo);

        $resized->toFile(public_path('storage/' . $newFileName));

        $this->user->photo = $newFileName;

        $this->user->save();
    }
}
