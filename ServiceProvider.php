<?php

namespace Tee\Portfolio;

use Tee\Portfolio\Widgets\PortfolioBoxList;
use Tee\System\Widget;
use Event;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function register()
    {
        // registra os widgets
        Widget::register(
            'portfolioBoxList',
            __NAMESPACE__.'\\Widgets\\PortfolioBoxList'
        );

        Event::listen('admin::menu.load', function($menu) {
            $format = '<img src="%s" class="fa" />&nbsp;&nbsp;<span>%s</span>';
            $menu->add(
                sprintf($format, moduleAsset('portfolio', 'images/icon_portfolio.png'), 'Portfólio'),
                route('admin.portfolio.index')
            );
        });
    }
}
