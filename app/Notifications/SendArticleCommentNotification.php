<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class SendArticleCommentNotification extends Notification
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
        logger($this->parseMessage($this->data));
        $subjectTmpl = "%s ( %s ) mentioned you in a comment in the following article";
        $subject = sprintf($subjectTmpl, $this->data['from']['name'], $this->data['from']['email']);
        return (new MailMessage())
            ->subject($subject)
            ->from($this->data['from']['email'], $this->data['from']['name'])
            ->markdown(
                'emails.article.comment.send',
                [
                    'article_title' => $this->data['article']['title'],
                    'link' => $this->createArticleCommentLink($this->data),
                    'message' => $this->parseMessage($this->data),
                    'subject' => $subject
                ]
            );
    }

    public function createArticleCommentLink($data): string
    {
        return env('ARTICLE_EDITOR_URL').'/'.$data['article']['id'].'#'.$data['hash'];
    }

    public function parseMessage($data)
    {
        return  preg_replace('/([A-z0-9_-]+\@[A-z0-9_-]+\.)([A-z0-9\_\-\.]{1,}[A-z])/',
            '[$1$2](mailto:$1$2)',
            $data['message']);
    }
}
