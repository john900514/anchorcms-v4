<?php

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $id, $role, $date, $modifier;

    /**
     * Create a new message instance.
     * @param User $user
     * @return void
     */
    public function __construct(string $id, string $role, string $date, string $modifier)
    {
        $this->id = $id;
        $this->role = $role;
        $this->date = $date;
        $this->modifier = $modifier;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = User::find($this->id);
        $modifier = User::find($this->modifier);

        return $this->from(
            env('MAIL_FROM_ADDRESS','developers@capeandbay.com'),
            env('MAIL_FROM_NAME', 'Cape & Bay Dev Team')
        )
            ->subject('You have been invited to join '.env('APP_NAME').' as an Admin!')
            ->view('emails.user.new-admin-email', [
                'new_user' => $user,
                'role' => $this->role,
                'date' => $this->date,
                'modify_user' => $modifier
            ]);
    }
}
