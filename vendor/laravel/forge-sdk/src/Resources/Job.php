<?php

namespace Laravel\Forge\Resources;

class Job extends Resource
{
    /**
     * The id of the job.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the server.
     *
     * @var int
     */
    public $serverId;

    /**
     * The command of the job.
     *
     * @var string
     */
    public $command;

    /**
     * The user that runs the job on the server.
     *
     * @var string
     */
    public $user;

    /**
     * How frequent the job will be running.
     *
     * @var string
     */
    public $frequency;

    /**
     * Cron representation of the job frequency.
     *
     * @var string
     */
    public $cron;

    /**
     * The status of the job.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the job was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Delete the given job.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteJob($this->serverId, $this->id);
    }
}
