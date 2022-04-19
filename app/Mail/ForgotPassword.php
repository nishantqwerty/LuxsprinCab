<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\Request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $email, string $url)
    {
        $this->email = $email;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $params = [
            'url' => $this->url
        ];

        return $this
            ->from(env('MAIL_USERNAME'))
            ->view('emails.forgotpassword')
            ->with($params)
            ->subject('Forgot password');
    }
}
