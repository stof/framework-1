<?php
declare(strict_types=1);
namespace Viserio\Mail\Transport;

use Aws\Ses\SesClient;
use Swift_Mime_Message;

class Ses extends AbstractTransport
{
    /**
     * The Amazon SES instance.
     *
     * @var \Aws\Ses\SesClient
     */
    protected $ses;

    /**
     * Create a new SES transport instance.
     *
     * @param \Aws\Ses\SesClient $ses
     */
    public function __construct(SesClient $ses)
    {
        $this->ses = $ses;
    }

    /**
     * Send Email.
     *
     * @param \Swift_Mime_Message $message
     * @param string[]|null       $failedRecipients
     *
     * @return int
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $headers = $message->getHeaders();

        $headers->addTextHeader('X-SES-Message-ID', $this->ses->sendRawEmail([
            'Source' => key($message->getSender() ?? $message->getFrom()),
            'RawMessage' => [
                'Data' => $message->toString(),
            ],
        ])->get('MessageId'));

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }
}
