<?php

declare(strict_types=1);

namespace OCA\TwoFactorWebEid\Service;

use DateTime;
use muzosh\web_eid_authtoken_validation_php\challenge\ChallengeNonce;
use muzosh\web_eid_authtoken_validation_php\challenge\ChallengeNonceStore;
use OCP\ISession;

class SessionBackedChallengeNonceStore extends ChallengeNonceStore
{
    private const CHALLENGE_NONCE_KEY = 'web-eid-challenge-nonce';

    private $session;

    public function __construct(ISession $session)
    {
        $this->session = $session;
    }

    /**
     * @UseSession
     */
    public function put(ChallengeNonce $challengeNonce): void
    {
        $this->session[self::CHALLENGE_NONCE_KEY] = serialize($challengeNonce);
    }

    /**
     * @UseSession
     */
    protected function getAndRemoveImpl(): ?ChallengeNonce
    {
        if (!$this->session[self::CHALLENGE_NONCE_KEY]){
            return null;
        }

        $challengeNonce = unserialize($this->session[self::CHALLENGE_NONCE_KEY], array(
            'allowed_classes' => array(ChallengeNonce::class, DateTime::class),
        ));

        if (!$challengeNonce) {
            return null;
        }

        unset($this->session[self::CHALLENGE_NONCE_KEY]);

        return $challengeNonce;
    }
}
