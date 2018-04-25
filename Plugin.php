<?php namespace Bree7e\BlogRelatedPosts;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    /**
     * @var array  Require the RainLab.Blog and PolloZen.MostVisited plugins
     */
    public $require = [
        'RainLab.Blog',
        'PKleindienst.BlogSeries'
    ];

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Bree7e\BlogRelatedPosts\Components\RelatedPosts' => 'RelatedPosts' // In Categories 
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'bree7e.blogrelatedposts.some_permission' => [
                'tab' => 'Test',
                'label' => 'Some permission'
            ],
        ];
    }
}
