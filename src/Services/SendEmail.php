<?php

namespace App\Services;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;

class SendEmail {

    private $targetEmail ;
    private $transport ;
    private $mailer ;
    private $contentText ;
    private $contentHTML ;
    private $subject ;

    public function __construct( string $targetEmail ) {

        $this->targetEmail = $targetEmail ;

        $this->transport = new GmailSmtpTransport(
            "blogmire@gmail.com" ,
            "nombreusessuites"
        ) ;

        $this->mailer = new Mailer( $this->transport ) ;
    }

    public function setContentText( string $contentText ): self {

        $this->contentText = $contentText ;

        return $this ;
    }

    public function getContentText(): ?string {

        return $this->contentText ;
    }

    public function setSubject( string $subject ) {

        $this->subject = $subject ;

        return $this;
    }

    public function getSubject(): ?string {

        return $this->subject ;
    }

    public function setContentHTML( string $contentHTML ): self {

        $this->contentHTML = $contentHTML ;

        return $this ;
    }

    public function getContentHTML(): ?string {

        return $this->contentHTML ;
    }

    public function send( ?int $priority = NULL ) {

        if( !$priority ) {

            $priority = Email::PRIORITY_NORMAL ;
        }

        $email = ( new Email( null ) )
            ->from( 'blogmire@gmail.com' )
            ->to( $this->targetEmail )
            ->priority( $priority )
            ->subject( $this->subject )
            ->text( $this->contentText )
            ->html( $this->contentHTML )
        ;

        $this->mailer->send( $email ) ;

    }
}