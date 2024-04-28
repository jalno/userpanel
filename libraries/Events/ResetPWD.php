<?php

namespace packages\userpanel\Events;

use packages\base\Event;
use packages\notifications\Notifiable;
use packages\userpanel\ResetPWD\Token;

use function packages\userpanel\Url;

class ResetPWD extends Event implements Notifiable
{
    public static function getName(): string
    {
        return 'userpanel_resetpwd_token';
    }

    public static function getParameters(): array
    {
        return [Token::class, 'link'];
    }

    protected Token $token;
    protected ?string $link = null;

    public function __construct(Token $token, ?string $link = null)
    {
        $this->token = $token;
        $this->link = $link;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return array<string,mixed>
     */
    public function getArguments(): array
    {
        if (!$this->link) {
            $this->generateLink();
        }

        return [
            'token' => $this->token,
            'link' => $this->link,
        ];
    }

    public function getTargetUsers(): array
    {
        return [$this->token->user];
    }

    protected function generateLink(): void
    {
        $this->link = url('resetpwd/token', [
            'token' => $this->token->token,
            'credential' => $this->token->user->email,
        ], true);
    }
}
