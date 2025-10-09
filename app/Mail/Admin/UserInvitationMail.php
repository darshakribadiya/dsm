<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Auth\UserInvitation;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $invitation;

    /**
     * Create a new message instance.
     */
    public function __construct(UserInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'User Invitation Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $frontendUrl = env('FRONTEND_URL');
        $invitationLink = $frontendUrl . '/accept-invitation?token=' . $this->invitation->token;

        return new Content(
            view: 'Admin.Mail.user-invitation',
            with: [
                'invitation' => $this->invitation,
                'invitationLink' => $invitationLink,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
