<?php

namespace App\Service;

use App\Message\SmsSend;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

/**
 * Class TalkbankSerializer
 * @author xagero
 * @package App\Service\Messenger\Transport\Serialization
 */
class Serializer implements SerializerInterface
{

    /**
     * Decodes an envelope and its message from an encoded-form.
     *
     * The `$encodedEnvelope` parameter is a key-value array that
     * describes the envelope and its content, that will be used by the different transports.
     *
     * The most common keys are:
     * - `body` (string) - the message body
     * - `headers` (string<string>) - a key/value pair of headers
     *
     * @param array $encodedEnvelope
     * @return Envelope
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        $json = \GuzzleHttp\json_decode($encodedEnvelope['body']);
        $message = new SmsSend($json->phone, $json->text);
        return new Envelope($message);
    }

    /**
     * Encodes an envelope content (message & stamps) to a common format understandable by transports.
     * The encoded array should only contain scalars and arrays.
     *
     * Stamps that implement NonSendableStampInterface should
     * not be encoded.
     *
     * The most common keys of the encoded array are:
     * - `body` (string) - the message body
     * - `headers` (string<string>) - a key/value pair of headers
     *
     * @param Envelope $envelope
     * @return array
     */
    public function encode(Envelope $envelope): array
    {
        /** @var SmsSend $message */
        $message = $envelope->getMessage();

        return [
            'body' => $message->__toString(),
        ];
    }
}
