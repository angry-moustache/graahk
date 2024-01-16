<?php

namespace App\Entities\Game;

use Illuminate\Support\Collection;

class Queue
{
    public Collection $jobs;

    public function __construct()
    {
        $this->jobs = collect();
    }

    public function push(Animation $job, array $board)
    {
        $this->jobs->push([
            'job' => $job,
            'board' => $board,
        ]);
    }

    public function send()
    {
        if ($this->jobs->isEmpty()) {
            return;
        }

        event(new \App\Events\UpdateQueue($this->jobs));
    }
}
