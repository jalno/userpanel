<?php

namespace themes\clipone;

use packages\base\Date;
use packages\base\Frontend\Theme;
use packages\base\Packages;
use packages\base\Translator;
use packages\base\View\Error;
use packages\userpanel;
use packages\userpanel\Authentication;
use packages\userpanel\Authorization;
use packages\userpanel\Frontend;

trait ViewTrait
{
    protected $bodyClasses = [];

    /** @var bool */
    protected $fixedHeader = true;

    /**
     * location of favicon.
     *
     * @var string
     */
    protected $favicon = '';

    public function the_header(string $template = ''): void
    {
        require dirname(__DIR__).'/html/parts/header/header'.($template ? '.'.$template : '').'.php';
    }

    public function the_footer(string $template = ''): void
    {
        require dirname(__DIR__) . '/html/parts/footer/footer'.($template ? '.'.$template : '').'.php';
    }

    public function getLogoHTML()
    {
        $logo = Frontend::getLogoHTML();
        if (!$logo) {
            $logo = 'CLIP<i class="clip-clip"></i>ONE';
        }

        return $logo;
    }

    public function getCopyRightHTML()
    {
        $copyright = Frontend::getCopyRightHTML();
        if (!$copyright) {
            $copyright = Date::Format('Y').' &copy; clip-one by cliptheme.';
        }

        return $copyright;
    }

    /**
     * Set Favicon for userpanel pages.
     *
     * @param string $icon must be full path to icon file
     */
    public function setFavicon(string $icon): void
    {
        $this->favicon = $icon;
    }

    /**
     * return full path of icon file.
     *
     * @return string that is full path to favicon logo or empty string if not set before
     */
    public function getFavicon(): string
    {
        return $this->favicon;
    }

    public function addBodyClass($class)
    {
        $this->bodyClasses[] = $class;
    }

    public function removeBodyClass($class)
    {
        if (($key = array_search($class, $this->bodyClasses)) !== false) {
            unset($this->bodyClasses[$key]);
        }
    }

    public function fixHeader(bool $fix = true): void
    {
        $this->fixedHeader = $fix;
    }

    public function staticHeader(bool $static = true): void
    {
        $this->fixedHeader = !$static;
    }

    public function isFixedHeader(): bool
    {
        return $this->fixedHeader;
    }

    protected function genBodyClasses()
    {
        $key = array_search('ltr', $this->bodyClasses);
        if (!in_array('rtl', $this->bodyClasses) and false === $key and Translator::isRTL()) {
            $this->addBodyClass('rtl');
        }
        if (false !== $key) {
            unset($this->bodyClasses[$key]);
        }

        return implode(' ', $this->bodyClasses);
    }

    /**
     * @param Error[]|null $errors that is array of errors you want to generate HTML for them
     *
     * @return string
     */
    protected function getErrorsHTML(?array $errors = null)
    {
        return array_reduce(
            $errors ?? $this->getErrors(),
            fn (string $carry, Error $error) => $carry.$this->getErrorHTML($error),
            ''
        );
    }

    /**
     * generate the HTML for an Error.
     */
    protected function getErrorHTML(Error $error): string
    {
        $code = '';
        $alert = [
            'type' => 'info',
            'txt' => $error->getMessage(),
            'title' => '',
            'closeable' => true,
        ];
        $data = $error->getData();
        if (!is_array($data)) {
            $data = [];
        }
        $alert = array_merge($alert, $data);
        if (!$alert['txt']) {
            $alert['txt'] = t('error.'.$error->getCode()) ?: $error->getCode();
        }
        switch ($error->getType()) {
            case Error::FATAL:
                $alert['type'] = 'danger';
                $alert['title'] = $alert['title'] ?: t('error.'.Error::FATAL.'.title');
                break;
            case Error::WARNING:
                $alert['type'] = 'warning';
                $alert['title'] = $alert['title'] ?: t('error.'.Error::WARNING.'.title');
                break;
            case Error::NOTICE:
                $alert['type'] = 'info';
                $alert['title'] = $alert['title'] ?: t('error.'.Error::NOTICE.'.title');
                break;
            case Error::SUCCESS:
                $alert['type'] = 'success';
                $alert['title'] = $alert['title'] ?: t('error.'.Error::NOTICE.'.title');
                break;
        }
        if (isset($alert['classes'])) {
            if (is_array($alert['classes'])) {
                $alert['classes'] = implode(' ', $alert['classes']);
            }
        } else {
            $alert['classes'] = '';
        }
        $code .= "<div class=\"alert alert-block alert-{$alert['type']} {$alert['classes']}\"".
            $this->buildAlertHtmlData($alert).'>';
        if ($alert['closeable']) {
            $code .= '<button data-dismiss="alert" class="close" type="button">&times;</button>';
        }
        $code .= '<h4 class="alert-heading">';
        switch ($alert['type']) {
            case 'danger': $code .= '<i class="fa fa-times-circle"></i>';
                break;
            case 'success': $code .= '<i class="fa fa-check-circle"></i>';
                break;
            case 'info': $code .= '<i class="fa fa-info-circle"></i>';
                break;
            case 'warning': $code .= '<i class="fa fa-exclamation-triangle"></i>';
                break;
        }

        $code .= " {$alert['title']}</h4><p>{$alert['txt']}</p>";

        if (isset($alert['btns']) and $alert['btns']) {
            $code .= '<p>';
            foreach ($alert['btns'] as $btn) {
                $code .= "<a href=\"{$btn['link']}\" class=\"btn {$btn['type']}\">{$btn['txt']}</a> ";
            }
            $code .= '</p>';
        }
        $code .= '</div>';

        return $code;
    }

    protected function canViewProfile()
    {
        return Authorization::is_accessed('profile_view');
    }

    protected function getSelfAvatarURL()
    {
        $user = Authentication::getUser();
        if ($user->avatar) {
            return Packages::package('userpanel')->url($user->avatar);
        } else {
            return Theme::url('assets/images/defaultavatar.jpg');
        }
    }

    private function buildAlertHtmlData($alert)
    {
        $code = '';
        if (isset($alert['data']) and $alert['data']) {
            foreach ($alert['data'] as $key => $value) {
                $code .= " data-{$key}=\"{$value}\" ";
            }
        }

        return $code;
    }
}
