<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCandidateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $candidate_name;

    public $subject;

    public $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($candidate_name, $subject, $body)
    {
        $this->candidate_name = $candidate_name;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = getFormattedTextByType('new_candidate', $this->candidate_name);
        $subject = $data['subject'];
        $message = $data['message'];

        return $this->subject($subject)->markdown($message);

        return $this->subject($this->subject)
            ->markdown('mails.candidate-email');
    }
}
