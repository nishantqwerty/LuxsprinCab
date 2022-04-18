<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\Request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotUsername extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $username;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $email, string $username)
    {
        $this->email = $email;
        $this->username = $username;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $params = [
            'username' => $this->username
        ];

        return $this
            ->from(env('MAIL_USERNAME'))
            ->view('emails.forgotusername')
            ->with($params)
            ->subject('Forgot Username');
    }
}
