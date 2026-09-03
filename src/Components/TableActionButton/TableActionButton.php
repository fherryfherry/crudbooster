<?php

namespace CrudBooster\Components\TableActionButton;

class TableActionButton
{
    private static array $options = [];
    private array $option;

    public function __construct(array $option)
    {
        $this->option = $option;
        return $this->updateOption();
    }
    public function confirmation(string $title, string $message)
    {
        $this->option['confirmation'] = [
            'title' => $title,
            'message' => $message
        ];
        return $this->updateOption();
    }
    public function icon(string $iconSvg)
    {
        $this->option['icon'] = $iconSvg;
        return $this->updateOption();
    }

    /**
     * @Deprecated
     * @param string $url
     * @return $this
     */
    public function url(string $url)
    {
        $this->option['action'] = function() use ($url) {
            return redirect($url);
        };
        return $this->updateOption();
    }
    public function action(callable $callback)
    {
        $this->option['action'] = $callback;
        return $this->updateOption();
    }
    public function actionRedirect(string $url)
    {
        $this->option['action'] = function() use ($url) {
            return redirect($url);
        };
        return $this->updateOption();
    }

    public function buttonSuccess()
    {
        $this->option['class'] = 'btn btn-success';
        return $this->updateOption();
    }
    public function buttonWarning()
    {
        $this->option['class'] = 'btn btn-warning';
        return $this->updateOption();
    }
    public function buttonDanger()
    {
        $this->option['class'] = 'btn btn-danger';
        return $this->updateOption();
    }
    public function buttonPrimary()
    {
        $this->option['class'] = 'btn btn-primary';
        return $this->updateOption();
    }
    public function buttonInfo()
    {
        $this->option['class'] = 'btn btn-info';
        return $this->updateOption();
    }
    public function buttonIconOnly()
    {
        $this->option['templateMode'] = 'ICON_ONLY';
        return $this->updateOption();
    }
    public function buttonTextOnly()
    {
        $this->option['templateMode'] = 'TEXT_ONLY';
        return $this->updateOption();
    }
    public function buttonIconText()
    {
        $this->option['templateMode'] = 'ICON_TEXT';
        return $this->updateOption();
    }

    private function updateOption(): static
    {
        app(TableActionButtonOptions::class)->setOption($this->option['label'], $this->option);
        return $this;
    }

    public static function __getOption(): array
    {
        return app(TableActionButtonOptions::class)->getOptions();
    }
}
