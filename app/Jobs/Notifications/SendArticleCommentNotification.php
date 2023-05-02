<?php

namespace App\Jobs\Notifications;

use App\Traits\QueueRpc;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendArticleCommentNotification as MailSendArticleCommentNotification;

class SendArticleCommentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, QueueRpc;

    private $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logEntry();
        Notification::route('mail', [
            $this->data['to']['email'] => $this->data['to']['name'],
        ])->notify(new MailSendArticleCommentNotification($this->data));
        //$this->sendReply('DONE');
    }

    protected function logEntry(){
        $this->logger = Log::channel('stderr');
        $this->logger->debug('SendArticleCommentNotification', [...$this->data]);
    }
}
