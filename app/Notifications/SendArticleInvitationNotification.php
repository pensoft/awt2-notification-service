<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class SendArticleInvitationNotification extends Notification
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $subjectTmpl = "%s ( %s ) invited you as a %s to article \"%s\"";
        $subject = sprintf(
            $subjectTmpl,
            $this->data['from']['name'],
            $this->data['from']['email'],
            $this->data['type'],
            $this->data['article']['title']);
        return (new MailMessage())
            ->subject($subject)
            ->from($this->data['from']['email'], $this->data['from']['name'])
            ->markdown(
                'emails.article.comment.send',
                [
                    'article_title' => $this->data['article']['title'],
                    'link' => $this->createArticleLink($this->data),
                    'message' => $this->parseMessage($this->data),
                    'subject' => $subject
                ]
            );
    }

    public function createArticleLink($data): string
    {
        return env('ARTICLE_EDITOR_URL').'/'.$data['article']['id'];
    }

    public function parseMessage($data)
    {
        return $data['message'];
    }
}
